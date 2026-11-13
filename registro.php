<?php require_once 'conexion.php'; ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Control - Biblioteca Sullivan</title>
    <!-- Tu archivo unificado de diseño externo -->
    <link rel="stylesheet" href="style.css">
    <link rel="icon" type="image/png" href="logobiblioteca.png">
</head>
<body class="body2">

    <!-- MAQUETA DEL DASHBOARD (IZQUIERDA: MENÚ | DERECHA: ALERTAS) -->
    <div class="dashboard-layout">
        
        <!-- COLUMNA IZQUIERDA: TU CONTENEDOR PADRE ORIGINAL -->
        <div class="columna-izquierda">
            <div class="contenedorpadrere" style="position: relative !important; top: 0 !important; left: 0 !important; transform: none !important; margin: 0; width: 100%; max-width: 450px;">
                <span class="Modulos">Panel de Módulos</span>
                <!-- 📊 CONTADORES RÁPIDOS AUTOMÁTICOS (KPIs) -->
<div class="contenedor-kpis" style="display: flex; gap: 15px; width: 100%; max-width: 400px; margin-bottom: 20px; box-sizing: border-box;">
    <?php
    // Consultas veloces a tu base de datos
    $total_libros = $conexion->query("SELECT SUM(Ejemplares_disponibles + IFNULL(Ejemplares_prestados, 0)) AS total FROM libros")->fetch_assoc()['total'] ?? 0;
    $total_usuarios = ($conexion->query("SELECT COUNT(*) AS total FROM alumnos")->fetch_assoc()['total'] ?? 0) + ($conexion->query("SELECT COUNT(*) AS total FROM profesores")->fetch_assoc()['total'] ?? 0);
   $prestamos_activos = $conexion->query("SELECT COUNT(*) AS total FROM prestamos WHERE fecha_devolucion_real IS NULL")->fetch_assoc()['total'] ?? 0;
    ?>
    <div class="tarjeta-kpi" style="flex: 1; background: #FFFFFF; padding: 12px; border-radius: 8px; text-align: center; box-shadow: 0 4px 12px rgba(15, 76, 129, 0.05); border: 1px solid #DDE6ED;">
        <span style="font-size: 20px; font-weight: bold; color: #0F4C81; display: block;"><?php echo $total_libros; ?></span>
        <span style="font-size: 11px; text-transform: uppercase; color: #64748b; font-weight: 600;">Libros</span>
    </div>
    <div class="tarjeta-kpi" style="flex: 1; background: #FFFFFF; padding: 12px; border-radius: 8px; text-align: center; box-shadow: 0 4px 12px rgba(15, 76, 129, 0.05); border: 1px solid #DDE6ED;">
        <span style="font-size: 20px; font-weight: bold; color: #2A9D8F; display: block;"><?php echo $total_usuarios; ?></span>
        <span style="font-size: 11px; text-transform: uppercase; color: #64748b; font-weight: 600;">Usuarios</span>
    </div>
    <div class="tarjeta-kpi" style="flex: 1; background: #FFFFFF; padding: 12px; border-radius: 8px; text-align: center; box-shadow: 0 4px 12px rgba(15, 76, 129, 0.05); border: 1px solid #DDE6ED;">
        <span style="font-size: 20px; font-weight: bold; color: #4a148c; display: block;"><?php echo $prestamos_activos; ?></span>
        <span style="font-size: 11px; text-transform: uppercase; color: #64748b; font-weight: 600;">Activos</span>
    </div>
</div>
                <!-- BLOQUE 1: GESTIÓN ESCOLAR E INVENTARIO -->
                <div style="width: 100%; text-align: center;">
                    <span class="subtitulo-menu">📝 Altas y Registros</span>
                    <a class="bton-1" href="profesores.php">👨‍🏫 Registrar Profesores</a>
                    <a class="bton-2" href="alumnos.php">👥 Registrar Alumnos</a>
                    <a class="bton-1" href="catalogo.php">✍️ Registrar Libros Nuevos</a>
                    <a class="bton-3" href="nuevo_prestamo.php">➕ Registrar Préstamo</a>
                    <a class="bton-4" href="ver_catalogo.php">📊 Ver Inventario Completo</a>
                </div>

                <!-- BLOQUE 2: OPERACIÓN DIARIA -->
                <div class="seccion-menu" style="width: 100%; text-align: center;">
                    <span class="subtitulo-menu">⚡ Operación de Préstamos</span>
                    <a class="bton-3" href="catalogo_interactivo.php">🟢 Catálogo Interactivo</a>
                    <a class="bton-4" href="prestamos.php">📋 Bitácora de Préstamos</a>
                    <a class="bton-4" href="retrasados.php" style="background-color: #FEE2E2 !important; color: #991B1B !important; border-color: #FCA5A5 !important;">⚠️ Entregas Retrasadas</a>
                </div>
            </div>
        </div>

        <!-- COLUMNA DERECHA: EL PANEL DE NOTIFICACIONES LATERAL -->
       <div class="panel-notificaciones-derecho">
    <h3 style="margin-top: 0; color: #0F4C81; font-size: 16px; border-bottom: 2px solid #E8EEF5; padding-bottom: 10px; display: flex; align-items: center; gap: 8px; text-transform: uppercase; letter-spacing: 0.5px;">
        🔔 Alertas de Devolución
    </h3>
    <div class="lista-alertas">
        <?php
        date_default_timezone_set('America/Mexico_City');
        $fecha_hoy = date('Y-m-d');
        
        // CORRECCIÓN DE COLUMNAS: id_Alumnos (con A mayúscula) e id_profesores (en plural)
        $sql_alertas = "SELECT 
                            l.Titulo AS Libro,
                            p.Cantidad AS CantidadLibros,
                            p.fecha_devolucion_esperada AS FechaLimite,
                            COALESCE(a.Nombre, prof.Nombre, 'Desconocido') AS Persona,
                            CASE WHEN a.Nombre IS NOT NULL THEN 'Alumno' ELSE 'Profesor' END AS TipoUsuario,
                            DATEDIFF(p.fecha_devolucion_esperada, '$fecha_hoy') AS DiasRestantes
                        FROM prestamos p
                        JOIN libros l ON p.id_libro = l.id_libro
                        LEFT JOIN alumnos a ON p.id_Alumnos = a.id_Alumnos
                        LEFT JOIN profesores prof ON p.id_profesores = prof.id_profesores
                        WHERE p.fecha_devolucion_real IS NULL 
                        ORDER BY p.fecha_devolucion_esperada ASC 
                        LIMIT 5";

        $res_alertas = $conexion->query($sql_alertas);

        if ($res_alertas && $res_alertas->num_rows > 0) {
            while($alerta = $res_alertas->fetch_assoc()) {
                $dias = (int)$alerta['DiasRestantes'];
                
                // Definir semáforo visual
                if ($dias < 0) {
                    $clase_aviso = "entrega-vencida";
                    $texto_tiempo = "⚠️ VENCIDO POR " . abs($dias) . " DÍAS";
                } elseif ($dias === 0) {
                    $clase_aviso = "entrega-hoy";
                    $texto_tiempo = "🚨 ENTREGA HOY";
                } else {
                    $clase_aviso = "entrega-proxima";
                    $texto_tiempo = "🟢 Quedan " . $dias . " días";
                }
                ?>
                <!-- Tarjeta de Alerta Limpia con solo su Contenido -->
                <div class="tarjeta-aviso <?php echo $clase_aviso; ?>" style="padding: 12px; border-radius: 8px; margin-bottom: 10px; border-left: 5px solid; font-size: 13px; line-height: 1.4;">
                    <span style="display: block; font-size: 11px; font-weight: bold; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 3px;">
                        <?php echo $texto_tiempo; ?>
                    </span>
                    <strong style="display: block; color: #1E293B; font-size: 14px;">
                        <?php echo $alerta['TipoUsuario'] . ": " . htmlspecialchars($alerta['Persona']); ?>
                    </strong>
                    <span style="display: block; color: #475569; margin-top: 2px;">
                        Libro: <i><?php echo htmlspecialchars($alerta['Libro']); ?></i>
                    </span>
                    <span style="display: block; color: #475569; font-size: 12px;">
                        Cantidad prestada: <b><?php echo (int)$alerta['CantidadLibros']; ?></b>
                    </span>
                </div>
                <?php
            }
        } else {
            echo '<div class="aviso-vacio">✅ No hay libros pendientes de devolución ni retrasos en el sistema.</div>';
        }
        ?>
    </div>
</div>
    <div class="z1">
      <a class="z2" href="index.html">REGRESAR</a>
  </div>
    <!-- Motor AJAX para conectar la Campanita de Alertas Inteligentes -->
<script>
function cargarAlertasCampana() {
    fetch('obtener_alertas.php')
        .then(response => response.text())
        .then(html => {
            // Busca tu contenedor de notificaciones y le inyecta las tarjetas
            const panel = document.querySelector('.panel-notificaciones-derecho');
            if (panel) {
                panel.innerHTML = html;
            }
        })
        .catch(error => console.error('Error cargando alertas:', error));
}

// Forzar a que las alertas se carguen en cuanto el usuario entre a la pantalla
window.onload = function() {
    cargarAlertasCampana();
};
</script>
<script>
function cargarAlertasCampana() {
    fetch('obtener_alertas.php')
        .then(response => response.text())
        .then(html => {
            const panel = document.querySelector('.panel-notificaciones-derecho');
            if (panel) { panel.innerHTML = html; }
        })
        .catch(error => console.error('Error cargando alertas:', error));
}

// ⚡ TRUCO DE USABILIDAD: Autofoco y salto con Enter en cualquier formulario de la pantalla
function activarAutofocoSmart() {
    const inputs = Array.from(document.querySelectorAll('input:not([readonly]):not([type="hidden"]), select'));
    if (inputs.length > 0) {
        inputs[0].focus(); // Posiciona el cursor automáticamente en el primer cuadro
        
        inputs.forEach((input, index) => {
            input.addEventListener('keydown', function(e) {
                if (key === 'Enter') {
                    e.preventDefault(); // Evita que se envíe el formulario antes de tiempo
                    const siguienteInput = inputs[index + 1];
                    if (siguienteInput) {
                        siguienteInput.focus(); // Salta al siguiente cuadro de texto
                    } else {
                        // Si era el último, busca el botón guardar y simula el clic
                        const botonGuardar = document.querySelector('.boton-guardar');
                        if (botonGuardar) botonGuardar.click();
                    }
                }
            });
        });
    }
}

// Ejecutamos todo en cuanto cargue el Menú Principal
window.onload = function() {
    cargarAlertasCampana();
    activarAutofocoSmart();
};
</script>
</body>
</html>