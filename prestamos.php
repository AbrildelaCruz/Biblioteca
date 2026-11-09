<?php
require_once 'conexion.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$mensaje = $_SESSION['mensaje'] ?? '';
$tipoMensaje = $_SESSION['tipoMensaje'] ?? '';
unset($_SESSION['mensaje'], $_SESSION['tipoMensaje']);

// ==========================================
// 1. PROCESAR ACCIÓN: REGISTRAR DEVOLUCIÓN
// ==========================================
if (isset($_GET['devolver_id'])) {
    $id_prestamo_dev = filter_input(INPUT_GET, 'devolver_id', FILTER_VALIDATE_INT);

    if ($id_prestamo_dev) {
        $fecha_real = date('Y-m-d');

        // Buscar qué libro es para devolverlo al inventario
        $stmt_buscar = $conexion->prepare("SELECT id_libro FROM prestamos WHERE id_prestamo = ?");
        $stmt_buscar->bind_param("i", $id_prestamo_dev);
        $stmt_buscar->execute();
        $res_buscar = $stmt_buscar->get_result()->fetch_assoc();
        $stmt_buscar->close();

        if ($res_buscar) {
            $id_libro_update = $res_buscar['id_libro'];

            // Marcar como devuelto
            $stmt_dev = $conexion->prepare("UPDATE prestamos SET fecha_devolucion_real = ?, estado_libro = 'Excelente' WHERE id_prestamo = ?");
            $stmt_dev->bind_param("si", $fecha_real, $id_prestamo_dev);

            if ($stmt_dev->execute()) {
                // Sumar 1 disponible y restar 1 prestado en la tabla de libros
                $stmt_upd_lib = $conexion->prepare("UPDATE libros SET Ejemplares_disponibles = Ejemplares_disponibles + 1, Ejemplares_prestados = GREATEST(0, Ejemplares_prestados - 1) WHERE id_libro = ?");
                $stmt_upd_lib->bind_param("i", $id_libro_update);
                $stmt_upd_lib->execute();
                $stmt_upd_lib->close();

                $_SESSION['mensaje'] = "🟢 Devolución asentada con éxito. El libro ha regresado al inventario disponible.";
                $_SESSION['tipoMensaje'] = 'exito';
            } else {
                $_SESSION['mensaje'] = "Error al asentar la devolución en la base de datos.";
                $_SESSION['tipoMensaje'] = 'error';
            }
            $stmt_dev->close();
        }
    }
    header("Location: prestamos.php");
    exit();
}

// 🚀 PROCESAR REPORTE DE LIBRO PERDIDO
// ===================================================
if (isset($_GET['perdido_id'])) {
    $id_prestamo_perdido = filter_input(INPUT_GET, 'perdido_id', FILTER_VALIDATE_INT);
    
    if ($id_prestamo_perdido) {
        $fecha_hoy = date('Y-m-d');
        
        // Buscar qué libro está asociado a este préstamo
        $stmt_buscar = $conexion->prepare("SELECT id_libro FROM prestamos WHERE id_prestamo = ?");
        $stmt_buscar->bind_param("i", $id_prestamo_perdido);
        $stmt_buscar->execute();
        $res_buscar = $stmt_buscar->get_result()->fetch_assoc();
        $stmt_buscar->close();
        
        if ($res_buscar) {
            $id_libro_update = $res_buscar['id_libro'];
            
            $conexion->begin_transaction();
            try {
                // Cerrar el préstamo marcándolo como 'Perdido' en el estado
                $stmt_perdido = $conexion->prepare("UPDATE prestamos SET fecha_devolucion_real = ?, estado_libro = 'Perdido' WHERE id_prestamo = ?");
                $stmt_perdido->bind_param("si", $fecha_hoy, $id_prestamo_perdido);
                $stmt_perdido->execute();
                $stmt_perdido->close();
                
                // AJUSTE DE INVENTARIO: Restamos 1 de Ejemplares Totales y 1 de Ejemplares Prestados
                $stmt_upd_lib = $conexion->prepare("UPDATE libros SET Ejemplares_totales = GREATEST(0, Ejemplares_totales - 1), Ejemplares_prestados = GREATEST(0, Ejemplares_prestados - 1) WHERE id_libro = ?");
                $stmt_upd_lib->bind_param("i", $id_libro_update);
                $stmt_upd_lib->execute();
                $stmt_upd_lib->close();
                
                $conexion->commit();
                $_SESSION['mensaje'] = "⚠️ Libro reportado como perdido. El inventario total ha sido descontado.";
                $_SESSION['tipoMensaje'] = 'error'; 
                
            } catch (Exception $e) {
                $conexion->rollback();
                $_SESSION['mensaje'] = "Error al procesar el extravío.";
                $_SESSION['tipoMensaje'] = 'error';
            }
        }
    }
    header("Location: prestamos.php");
    exit();
}

// ==========================================
// 🛠️ CONFIGURACIÓN GLOBAL DEL PAGINADOR
// ==========================================
$registros_por_pagina = 12; // Cantidad de movimientos por vista

$pagina_actual = isset($_GET['pagina']) ? filter_input(INPUT_GET, 'pagina', FILTER_VALIDATE_INT) : 1;
if (!$pagina_actual || $pagina_actual < 1) {
    $pagina_actual = 1;
}

$inicio = ($pagina_actual - 1) * $registros_por_pagina;

// 1. Contar total absoluto de préstamos
$total_res = $conexion->query("SELECT COUNT(*) AS total FROM prestamos");
$total_filas = $total_res->fetch_assoc()['total'];
$total_paginas = ceil($total_filas / $registros_por_pagina);

// ==========================================
// 2. CONSULTAR EL HISTORIAL DE PRÉSTAMOS
// ==========================================
$sql_bitacora = "SELECT 
                    p.id_prestamo,
                    l.Titulo AS Libro,
                    COALESCE(a.Nombre, prof.Nombre, 'Desconocido') AS Persona,
                    CASE WHEN a.Nombre IS NOT NULL THEN 'Alumno' ELSE 'Profesor' END AS TipoPersona,
                    p.fecha_salida,
                    p.fecha_devolucion_esperada,
                    p.fecha_devolucion_real,
                    p.tipo_movimiento
                 FROM prestamos p
                 JOIN libros l ON p.id_libro = l.id_libro
                 LEFT JOIN alumnos a ON p.id_alumnos = a.id_Alumnos
                 LEFT JOIN profesores prof ON p.id_profesores = prof.id_profesores
                 ORDER BY p.id_prestamo DESC
                 LIMIT $registros_por_pagina OFFSET $inicio";

$resultado_bitacora = $conexion->query($sql_bitacora);
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bitácora General de Préstamos - Biblioteca Sullivan</title>
    <link rel="stylesheet" href="style.css?v=<?php echo time(); ?>">
    <link rel="icon" type="image/png" href="logobiblioteca.png">
    <style>
        /* Estilos limpios y estéticos para el paginador */
        .paginacion { text-align: center; margin: 30px auto; }
        .paginacion a { display: inline-block; padding: 8px 14px; margin: 0 4px; background-color: #f1f5f9; color: #1e293b; text-decoration: none; border-radius: 4px; border: 1px solid #cbd5e1; font-weight: bold; font-size: 13px; }
        .paginacion a.activa { background-color: #0F4C81; color: white; border-color: #0F4C81; }
        .paginacion a:hover:not(.activa) { background-color: #e2e8f0; }
        .paginacion span.desactivado { display: inline-block; padding: 8px 14px; margin: 0 4px; background-color: #f8fafc; color: #94a3b8; border-radius: 4px; border: 1px solid #e2e8f0; font-size: 13px; cursor: not-allowed; }
    </style>
</head>

<body class="body2">

    <div class="contenedor-burbujas">
        <div class="burbuja"></div>
        <div class="burbuja"></div>
        <div class="burbuja"></div>
    </div>

    <?php if ($mensaje !== ''): ?>
        <div class="alerta <?php echo $tipoMensaje; ?>"
            style="padding: 10px; margin: 15px auto; max-width: 850px; text-align: center; border-radius: 5px; font-weight: bold; background-color: <?php echo $tipoMensaje === 'exito' ? '#d4edda' : '#f8d7da'; ?>; color: <?php echo $tipoMensaje === 'exito' ? '#155724' : '#721c24'; ?>;">
            <?php echo $mensaje; ?>
        </div>
    <?php endif; ?>

    <h2 style="text-align: center; color: #0F4C81; margin-top: 30px;">📋 Bitácora General de Préstamos y Apartados</h2>
    <p style="text-align: center; color: #475569; margin-bottom: 20px;">Cuaderno digital de control para registrar devoluciones y revisar el historial.</p>

    <!-- 🔍 BARRA DE BÚSQUEDA EN TIEMPO REAL PARA LA BITÁCORA -->
    <div class="contenedor-busqueda" style="width: 95%; max-width: 500px; margin: 0 auto 20px auto; text-align: center;">
        <input type="text" id="buscador-bitacora" class="input-busqueda" 
               placeholder="🔍 Busca por título de libro o nombre de usuario..." 
               style="width: 100%; padding: 12px 20px; font-size: 15px; border: 2px solid #DDE6ED; border-radius: 30px; outline: none; box-sizing: border-box; box-shadow: 0 4px 6px rgba(15, 76, 129, 0.03);">
    </div>

    <table class="tabla-datos" style="max-width: 95%; margin: 20px auto; font-size: 13px;">
        <thead>
            <tr>
                <th>ID</th>
                <th>Libro Solicitado</th>
                <th>Usuario</th>
                <th>Ocupación</th>
                <th>Tipo</th>
                <th>Fecha Salida</th>
                <th>Fecha Límite</th>
                <th>Fecha Devolución</th>
                <th>Acción</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($resultado_bitacora && $resultado_bitacora->num_rows > 0): ?>
                <?php 
                date_default_timezone_set('America/Mexico_City');
                $fecha_hoy_timestamp = strtotime(date('Y-m-d'));
                
                // 🟢 CONTADOR DINÁMICO DESCENDENTE:
                // Calcula el número más alto correspondiente al primer registro de esta página
                $contador_visual = $total_filas - $inicio;
                
                while ($fila = $resultado_bitacora->fetch_assoc()):
                    $entregado = ($fila['fecha_devolucion_real'] !== null);
                    $es_apartado = ($fila['tipo_movimiento'] === 'Apartado');
                    // Cálculo dinámico para la asignación de color a la fila
                    $clase_fila = ''; 
                    if (!$entregado) {
                        $fecha_limite_timestamp = strtotime($fila['fecha_devolucion_esperada']);
                        $segundos_diferencia = $fecha_limite_timestamp - $fecha_hoy_timestamp;
                        $dias_restantes = round($segundos_diferencia / (60 * 60 * 24));
                        
                        if ($dias_restantes < 0) {
                            $clase_fila = 'entrega-vencida';
                        } elseif ($dias_restantes == 0) {
                            $clase_fila = 'entrega-hoy';
                        } else {
                            $clase_fila = 'entrega-proxima';
                        }
                    }
                    ?>
                    <tr class="<?php echo $clase_fila; ?>">
                        <!-- Imprime el conteo numérico de la fila de forma ordenada -->
                        <td style="font-weight: bold; text-align: center;"><?php echo $contador_visual; ?></td>
                        <td><b><?php echo htmlspecialchars($fila['Libro']); ?></b></td>
                        <td><u><?php echo htmlspecialchars($fila['Persona']); ?></u></td>
                        <td><?php echo $fila['TipoPersona']; ?></td>
                        <td>
                            <span style="padding: 2px 6px; border-radius: 3px; font-size: 11px; font-weight: bold; background-color: <?php echo $es_apartado ? '#FFFBEB' : '#EFF6FF'; ?>; color: <?php echo $es_apartado ? '#92400E' : '#1E40AF'; ?>;">
                                <?php echo htmlspecialchars($fila['tipo_movimiento'] ?? 'Prestado'); ?>
                            </span>
                        </td>
                        <td><?php echo date('d/m/Y', strtotime($fila['fecha_salida'])); ?></td>
                        <td style="font-weight: bold;"><?php echo date('d/m/Y', strtotime($fila['fecha_devolucion_esperada'])); ?></td>
                        <td>
                            <?php if ($entregado): ?>
                                <span style="color: green; font-weight: bold;">✔ <?php echo date('d/m/Y', strtotime($fila['fecha_devolucion_real'])); ?></span>
                            <?php else: ?>
                                <span style="color: #475569; font-style: italic; font-weight: bold;">Pendiente</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if (!$entregado): ?>
                                <!-- 1. Botón Recibir -->
                                <a href="prestamos.php?devolver_id=<?php echo $fila['id_prestamo']; ?>" style="text-decoration: none; padding: 4px 8px; background-color: #2A9D8F; border-radius: 4px; color: white; font-size: 11px; font-weight: bold; display: inline-block;" onclick="return confirm('¿Confirmas la entrega física del libro?');">📥 Recibir</a>
                                 
                                <!-- 2. Botón Renovar -->
                                <button type="button" 
                                        onclick="prepararRenovacion('<?php echo strtolower($fila['TipoPersona']); ?>', '<?php echo htmlspecialchars($fila['Persona'], ENT_QUOTES, 'UTF-8'); ?>', '<?php echo htmlspecialchars($fila['Libro'], ENT_QUOTES, 'UTF-8'); ?>')" 
                                        style="padding: 4px 8px; background-color: #0F4C81; border: none; border-radius: 4px; color: white; font-size: 11px; font-weight: bold; display: inline-block; margin-left: 4px; cursor: pointer; font-family: 'Segoe UI', Arial, sans-serif;">
                                    🔄 Renovar
                                </button>

                                <!-- 3. Botón Perdido -->
                                <a href="prestamos.php?perdido_id=<?php echo $fila['id_prestamo']; ?>" style="text-decoration: none; padding: 4px 8px; background-color: #dc3545; border-radius: 4px; color: white; font-size: 11px; font-weight: bold; display: inline-block; margin-left: 4px;" onclick="return confirm('⚠️ ¿Seguro que deseas reportar este libro como PERDIDO permanentemente? Esto afectará los totales.');">❌ Perdido</a>
                            <?php else: ?>
                                <span style="font-weight: bold; color: #475569;">Completado</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php 
                    $contador_visual--; // Restamos para la siguiente fila
                endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="9" style="text-align: center; font-weight: bold; padding: 20px;">No se ha registrado ningún préstamo o apartado en el sistema todavía.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
     <!-- ==========================================
    🎮 CONTROLADORES VISUALES DE LA PAGINACIÓN
    ========================================== -->
    <?php if ($total_paginas > 1): ?>
        <div class="paginacion">
          
          <!-- Botón de Página Anterior -->
          <?php if ($pagina_actual > 1): ?>
            <a href="prestamos.php?pagina=<?php echo $pagina_actual - 1; ?>">« Anterior</a>
          <?php else: ?>
            <span class="desactivado">« Anterior</span>
          <?php endif; ?>

          <!-- Números de Páginas Dinámicos (1, 2, 3...) -->
          <?php for ($i = 1; $i <= $total_paginas; $i++): ?>
            <a href="prestamos.php?pagina=<?php echo $i; ?>" class="<?php echo ($i === $pagina_actual) ? 'activa' : ''; ?>">
              <?php echo $i; ?>
            </a>
          <?php endfor; ?>

          <!-- Botón de Página Siguiente -->
          <?php if ($pagina_actual < $total_paginas): ?>
            <a href="prestamos.php?pagina=<?php echo $pagina_actual + 1; ?>">Siguiente »</a>
          <?php else: ?>
            <span class="desactivado">Siguiente »</span>
          <?php endif; ?>

        </div>
    <?php endif; ?>

    <!-- Botón Regresar de la Bitácora -->
    <div class="z1">
        <a class="z2" href="registro.php">REGRESAR</a>
    </div>

    <!-- ==========================================================
         ZONA DE SCRIPTS JAVASCRIPT UNIFICADA AL FINAL
    ========================================================== -->
    <script>
    // 1. MOTOR DE FILTRADO EN TIEMPO REAL PARA LA BITÁCORA
    document.getElementById('buscador-bitacora').addEventListener('input', function () {
        var filtro = this.value.toLowerCase().trim();
        var filas = document.querySelectorAll('.tabla-datos tbody tr');

        filas.forEach(function(fila) {
            // Si la fila muestra el mensaje de que "no hay registros", la saltamos
            if (fila.cells.length < 3) return;

            var textoLibro = fila.cells[1].textContent.toLowerCase();
            var textoPersona = fila.cells[2].textContent.toLowerCase();

            // Si coincide con el título del libro o con el nombre de la persona, la muestra
            if (textoLibro.includes(filtro) || textoPersona.includes(filtro)) {
                fila.style.display = '';
            } else {
                fila.style.display = 'none';
            }
        });
    });

    // 2. SISTEMA DE LOGÍSTICA PARA LA MEMORIA DE RENOVACIÓN
    function prepararRenovacion(rol, nombrePersona, tituloLibro) {
        sessionStorage.setItem('renovar_rol', rol);
        sessionStorage.setItem('renovar_nombre', nombrePersona);
        sessionStorage.setItem('renovar_libro', tituloLibro);
        window.location.href = 'nuevo_prestamo.php';
    }
    </script>
</body>
</html>