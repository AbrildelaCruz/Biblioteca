<?php
require_once 'conexion.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

date_default_timezone_set('America/Mexico_City');
$fecha_hoy = date('Y-m-d');

// Filtro activo (todos, pendientes, vencidos, devueltos)
$filtro_actual = $_GET['filtro'] ?? 'todos';
$filtros_validos = ['todos', 'pendientes', 'vencidos', 'devueltos'];
if (!in_array($filtro_actual, $filtros_validos, true)) {
    $filtro_actual = 'todos';
}

$mensaje = $_SESSION['mensaje'] ?? '';
$tipoMensaje = $_SESSION['tipoMensaje'] ?? '';
$sonar_campana = $_SESSION['sonar_campana'] ?? false;
unset($_SESSION['mensaje'], $_SESSION['tipoMensaje'], $_SESSION['sonar_campana']);

// ==========================================
// 1. PROCESAR ACCIÓN: REGISTRAR DEVOLUCIÓN
// ==========================================
if (isset($_GET['devolver_id'])) {
    $id_prestamo_dev = filter_input(INPUT_GET, 'devolver_id', FILTER_VALIDATE_INT);

    if ($id_prestamo_dev) {
        $fecha_real = date('Y-m-d');

        $stmt_buscar = $conexion->prepare("SELECT id_libro FROM prestamos WHERE id_prestamo = ?");
        $stmt_buscar->bind_param("i", $id_prestamo_dev);
        $stmt_buscar->execute();
        $res_buscar = $stmt_buscar->get_result()->fetch_assoc();
        $stmt_buscar->close();

        if ($res_buscar) {
            $id_libro_update = $res_buscar['id_libro'];

            $stmt_dev = $conexion->prepare("UPDATE prestamos SET fecha_devolucion_real = ?, estado_libro = 'Excelente' WHERE id_prestamo = ?");
            $stmt_dev->bind_param("si", $fecha_real, $id_prestamo_dev);

            if ($stmt_dev->execute()) {
                $stmt_upd_lib = $conexion->prepare("UPDATE libros SET Ejemplares_disponibles = Ejemplares_disponibles + 1, Ejemplares_prestados = GREATEST(0, Ejemplares_prestados - 1) WHERE id_libro = ?");
                $stmt_upd_lib->bind_param("i", $id_libro_update);
                $stmt_upd_lib->execute();
                $stmt_upd_lib->close();

                $_SESSION['mensaje'] = "🟢 Devolución realizada con éxito. El libro ha regresado al inventario disponible.";
                $_SESSION['tipoMensaje'] = 'exito';
                $_SESSION['sonar_campana'] = true;
            } else {
                $_SESSION['mensaje'] = "Error al asentar la devolución en la base de datos.";
                $_SESSION['tipoMensaje'] = 'error';
            }
            $stmt_dev->close();
        }
    }
    header("Location: prestamos.php?filtro=" . urlencode($filtro_actual));
    exit();
}

// ==========================================
// 2. PROCESAR ACCIÓN: RENOVAR PRÉSTAMO (+7 DÍAS ILIMITADO)
// ==========================================
if (isset($_GET['renovar_id'])) {
    $id_prestamo_ren = filter_input(INPUT_GET, 'renovar_id', FILTER_VALIDATE_INT);

    if ($id_prestamo_ren) {
        $stmt_check = $conexion->prepare("SELECT fecha_devolucion_real, IFNULL(Renovacion, 0) AS veces_renovado FROM prestamos WHERE id_prestamo = ?");
        $stmt_check->bind_param("i", $id_prestamo_ren);
        $stmt_check->execute();
        $res_check = $stmt_check->get_result()->fetch_assoc();
        $stmt_check->close();

        if ($res_check && $res_check['fecha_devolucion_real'] === null) {
            $nuevo_conteo = intval($res_check['veces_renovado']) + 1;

            $stmt_ren = $conexion->prepare("UPDATE prestamos SET fecha_devolucion_esperada = DATE_ADD(fecha_devolucion_esperada, INTERVAL 7 DAY), estado_libro = 'Renovado', Renovacion = ? WHERE id_prestamo = ?");
            $stmt_ren->bind_param("ii", $nuevo_conteo, $id_prestamo_ren);

            if ($stmt_ren->execute()) {
                $_SESSION['mensaje'] = "🔄 Préstamo renovado con éxito (+7 días). Total de renovaciones de este libro: $nuevo_conteo.";
                $_SESSION['tipoMensaje'] = 'exito';
            } else {
                $_SESSION['mensaje'] = "Error al renovar el préstamo en la base de datos.";
                $_SESSION['tipoMensaje'] = 'error';
            }
            $stmt_ren->close();
        } else {
            $_SESSION['mensaje'] = "⚠️ Este libro ya fue devuelto y no se puede renovar.";
            $_SESSION['tipoMensaje'] = 'error';
        }
    }
    header("Location: prestamos.php?filtro=" . urlencode($filtro_actual));
    exit();
}

// ==========================================
// 3. PROCESAR REPORTE DE LIBRO PERDIDO
// ==========================================
if (isset($_GET['perdido_id'])) {
    $id_prestamo_perdido = filter_input(INPUT_GET, 'perdido_id', FILTER_VALIDATE_INT);
    
    if ($id_prestamo_perdido) {
        $stmt_buscar = $conexion->prepare("SELECT id_libro FROM prestamos WHERE id_prestamo = ?");
        $stmt_buscar->bind_param("i", $id_prestamo_perdido);
        $stmt_buscar->execute();
        $res_buscar = $stmt_buscar->get_result()->fetch_assoc();
        $stmt_buscar->close();
        
        if ($res_buscar) {
            $id_libro_update = $res_buscar['id_libro'];
            
            $conexion->begin_transaction();
            try {
                $stmt_perdido = $conexion->prepare("UPDATE prestamos SET fecha_devolucion_real = ?, estado_libro = 'Perdido' WHERE id_prestamo = ?");
                $stmt_perdido->bind_param("si", $fecha_hoy, $id_prestamo_perdido);
                $stmt_perdido->execute();
                $stmt_perdido->close();
                
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
    header("Location: prestamos.php?filtro=" . urlencode($filtro_actual));
    exit();
}

// ==========================================
// CONTADORES PARA LAS PESTAÑAS DE FILTRO
// ==========================================
$conteo_todos = $conexion->query("SELECT COUNT(*) AS total FROM prestamos")->fetch_assoc()['total'] ?? 0;
$conteo_pendientes = $conexion->query("SELECT COUNT(*) AS total FROM prestamos WHERE fecha_devolucion_real IS NULL")->fetch_assoc()['total'] ?? 0;
$conteo_vencidos = $conexion->query("SELECT COUNT(*) AS total FROM prestamos WHERE fecha_devolucion_real IS NULL AND fecha_devolucion_esperada < '$fecha_hoy'")->fetch_assoc()['total'] ?? 0;
$conteo_devueltos = $conexion->query("SELECT COUNT(*) AS total FROM prestamos WHERE fecha_devolucion_real IS NOT NULL")->fetch_assoc()['total'] ?? 0;

$condicion_where = "";
if ($filtro_actual === 'pendientes') {
    $condicion_where = "WHERE p.fecha_devolucion_real IS NULL";
} elseif ($filtro_actual === 'vencidos') {
    $condicion_where = "WHERE p.fecha_devolucion_real IS NULL AND p.fecha_devolucion_esperada < '$fecha_hoy'";
} elseif ($filtro_actual === 'devueltos') {
    $condicion_where = "WHERE p.fecha_devolucion_real IS NOT NULL";
}

// ==========================================
// CONFIGURACIÓN GLOBAL DEL PAGINADOR
// ==========================================
$registros_por_pagina = 12;

$pagina_actual = isset($_GET['pagina']) ? filter_input(INPUT_GET, 'pagina', FILTER_VALIDATE_INT) : 1;
if (!$pagina_actual || $pagina_actual < 1) {
    $pagina_actual = 1;
}

$inicio = ($pagina_actual - 1) * $registros_por_pagina;

$total_res = $conexion->query("SELECT COUNT(*) AS total FROM prestamos p $condicion_where");
$total_filas = $total_res->fetch_assoc()['total'] ?? 0;
$total_paginas = ceil($total_filas / $registros_por_pagina);

// ==========================================
// CONSULTAR EL HISTORIAL DE PRÉSTAMOS
// ==========================================
$sql_bitacora = "SELECT 
                    p.id_prestamo,
                    l.Titulo AS Libro,
                    COALESCE(
                        NULLIF(TRIM(CONCAT_WS(' ', a.Nombre, a.Apellido_Paterno, a.Apellido_Materno)), ''),
                        NULLIF(TRIM(CONCAT_WS(' ', prof.Nombre, prof.Apellido_Paterno, prof.Apellido_Materno)), ''),
                        'Desconocido'
                    ) AS Persona,
                    CASE WHEN a.id_Alumnos IS NOT NULL THEN 'Alumno' ELSE 'Profesor' END AS TipoPersona,
                    p.fecha_salida,
                    p.fecha_devolucion_esperada,
                    p.fecha_devolucion_real,
                    p.tipo_movimiento,
                    p.estado_libro,
                    IFNULL(p.Renovacion, 0) AS veces_renovado
                 FROM prestamos p
                 JOIN libros l ON p.id_libro = l.id_libro
                 LEFT JOIN alumnos a ON p.id_alumnos = a.id_Alumnos
                 LEFT JOIN profesores prof ON p.id_profesores = prof.id_profesores
                 $condicion_where
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
</head>

<body class="body2">

    <?php if ($mensaje !== ''): ?>
        <div class="alerta <?php echo $tipoMensaje; ?>"
        style="padding: 12px; margin: 15px auto; max-width: 850px; text-align: center; border-radius: 8px; font-weight: bold; background-color: <?php echo $tipoMensaje === 'exito' ? '#d4edda' : '#f8d7da'; ?>; color: <?php echo $tipoMensaje === 'exito' ? '#155724' : '#721c24'; ?>;">
            <?php if ($sonar_campana): ?>
                <span id="campana-devolucion" class="campanita-icono sonar" onclick="tocarSonidoCampanita()" style="margin-right: 6px;">🔔</span>
            <?php endif; ?>
            <?php echo $mensaje; ?>
        </div>
    <?php endif; ?>

    <h2 style="text-align: center; color: #0F4C81; margin-top: 30px;">📋 Bitácora General de Préstamos y Apartados</h2>
    <p style="text-align: center; color: #475569; margin-bottom: 20px;">Registro de devoluciones, renovaciones y revisión del historial.</p>

    <div class="barra-filtros">
        <a href="prestamos.php?filtro=todos" class="tab-filtro <?php echo $filtro_actual === 'todos' ? 'activa' : ''; ?>">
            📋 Todos <span class="badge-filtro"><?php echo $conteo_todos; ?></span>
        </a>
        <a href="prestamos.php?filtro=pendientes" class="tab-filtro <?php echo $filtro_actual === 'pendientes' ? 'activa' : ''; ?>">
            ⏳ Pendientes <span class="badge-filtro"><?php echo $conteo_pendientes; ?></span>
        </a>
        <a href="prestamos.php?filtro=vencidos" class="tab-filtro <?php echo $filtro_actual === 'vencidos' ? 'activa-vencidos' : ''; ?>">
            ⚠️ Vencidos <span class="badge-filtro"><?php echo $conteo_vencidos; ?></span>
        </a>
        <a href="prestamos.php?filtro=devueltos" class="tab-filtro <?php echo $filtro_actual === 'devueltos' ? 'activa-devueltos' : ''; ?>">
            ✅ Devueltos <span class="badge-filtro"><?php echo $conteo_devueltos; ?></span>
        </a>
    </div>

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
                $fecha_hoy_timestamp = strtotime($fecha_hoy);
                $contador_visual = $total_filas - $inicio;
                
                while ($fila = $resultado_bitacora->fetch_assoc()):
                    $entregado = ($fila['fecha_devolucion_real'] !== null);
                    $es_apartado = ($fila['tipo_movimiento'] === 'Apartado');
                    $es_perdido = ($fila['estado_libro'] === 'Perdido');
                    $veces_renovado = (int)($fila['veces_renovado'] ?? 0);
                    
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
                        <td>
                            <strong><?php echo date('d/m/Y', strtotime($fila['fecha_devolucion_esperada'])); ?></strong>
                            <?php if ($veces_renovado > 0): ?>
                                <span class="badge-renovaciones">
                                    🔄 Renovado: <?php echo $veces_renovado; ?> <?php echo ($veces_renovado === 1) ? 'vez' : 'veces'; ?>
                                </span>
                            <?php else: ?>
                                <span class="sin-renovaciones">Sin renovaciones</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($entregado): ?>
                                <?php if ($es_perdido): ?>
                                    <span style="color: #DC3545; font-weight: bold;">❌ Perdido (<?php echo date('d/m/Y', strtotime($fila['fecha_devolucion_real'])); ?>)</span>
                                <?php else: ?>
                                    <span style="color: green; font-weight: bold;">✔ <?php echo date('d/m/Y', strtotime($fila['fecha_devolucion_real'])); ?></span>
                                <?php endif; ?>
                            <?php else: ?>
                                <span style="color: #475569; font-style: italic; font-weight: bold;">Pendiente</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if (!$entregado): ?>
                                <a href="prestamos.php?filtro=<?php echo urlencode($filtro_actual); ?>&devolver_id=<?php echo $fila['id_prestamo']; ?>" class="btn-accion-recibir" onclick="return confirm('¿Confirmas la entrega física del libro?');">📥 Recibir</a>
                                <a href="prestamos.php?filtro=<?php echo urlencode($filtro_actual); ?>&renovar_id=<?php echo $fila['id_prestamo']; ?>" class="btn-accion-renovar" onclick="return confirm('¿Deseas renovar este préstamo por 7 días adicionales?');">🔄 Renovar (+7d)</a>
                                <a href="prestamos.php?filtro=<?php echo urlencode($filtro_actual); ?>&perdido_id=<?php echo $fila['id_prestamo']; ?>" class="btn-accion-perdido" onclick="return confirm('⚠️ ¿Seguro que deseas reportar este libro como PERDIDO permanentemente? Esto afectará los totales.');">❌ Perdido</a>
                            <?php else: ?>
                                <span class="texto-completado">Completado</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php 
                    $contador_visual--;
                endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="9" style="text-align: center; font-weight: bold; padding: 20px;">No hay registros que coincidan.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <?php if ($total_paginas > 1): ?>
        <div class="paginacion">
          <?php if ($pagina_actual > 1): ?>
            <a href="prestamos.php?filtro=<?php echo urlencode($filtro_actual); ?>&pagina=<?php echo $pagina_actual - 1; ?>">« Anterior</a>
          <?php else: ?>
            <span class="desactivado">Anterior</span>
          <?php endif; ?>

          <?php for ($i = 1; $i <= $total_paginas; $i++): ?>
            <a href="prestamos.php?filtro=<?php echo urlencode($filtro_actual); ?>&pagina=<?php echo $i; ?>" class="<?php echo ($i === $pagina_actual) ? 'activa' : ''; ?>">
              <?php echo $i; ?>
            </a>
          <?php endfor; ?>

          <?php if ($pagina_actual < $total_paginas): ?>
            <a href="prestamos.php?filtro=<?php echo urlencode($filtro_actual); ?>&pagina=<?php echo $pagina_actual + 1; ?>">Siguiente</a>
          <?php else: ?>
            <span class="desactivado">Siguiente</span>
          <?php endif; ?>
        </div>
    <?php endif; ?>

    <div class="z1">
        <a class="z2" href="registro.php">REGRESAR</a>
    </div>

    <?php if ($sonar_campana): ?>
    <script>
    function tocarSonidoCampanita() {
        const AudioCtx = window.AudioContext || window.webkitAudioContext;
        if (!AudioCtx) return;
        const ctx = new AudioCtx();
        if (ctx.state === 'suspended') ctx.resume();

        const ahora = ctx.currentTime;
        [1318.5, 1975.5].forEach((freq, i) => {
            const osc = ctx.createOscillator();
            const ganancia = ctx.createGain();
            osc.type = 'sine';
            osc.frequency.setValueAtTime(freq, ahora + i * 0.09);
            ganancia.gain.setValueAtTime(0.18, ahora + i * 0.09);
             ganancia.gain.exponentialRampToValueAtTime(0.0001, ahora + i * 0.09 + 0.95);
            osc.connect(ganancia);
            ganancia.connect(ctx.destination);
            osc.start(ahora + i * 0.09);
            osc.stop(ahora + i * 0.09 + 0.95);
        });
    }

    window.addEventListener('DOMContentLoaded', () => {
        tocarSonidoCampanita();
    });
    </script>
    <?php endif; ?>

</body>
</html>