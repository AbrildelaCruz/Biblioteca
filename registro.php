<?php require_once 'conexion.php'; ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Control - Biblioteca Sullivan</title>
    <!-- Tu archivo unificado de diseño externo -->
    <link rel="stylesheet" href="style.css">
</head>
<body class="body2">

    <div class="contenedor-burbujas">
        <div class="burbuja"></div>
        <div class="burbuja"></div>
        <div class="burbuja"></div>
    </div>

    <!-- MAQUETA DEL DASHBOARD (IZQUIERDA: MENÚ | DERECHA: ALERTAS) -->
    <div class="dashboard-layout">
        
        <!-- COLUMNA IZQUIERDA: TU CONTENEDOR PADRE ORIGINAL -->
        <div class="columna-izquierda">
            <div class="contenedorpadrere" style="position: relative !important; top: 0 !important; left: 0 !important; transform: none !important; margin: 0; width: 100%; max-width: 450px;">
                <span class="Modulos">Panel de Módulos</span>
                
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
        
        // Consulta exacta uniendo préstamos con alumnos, profesores y libros
       $sql_alertas = "SELECT 
                    l.Titulo AS Libro,
                    p.fecha_devolucion_esperada AS FechaLimite,
                    COALESCE(a.Nombre, prof.Nombre, 'Desconocido') AS Persona,
                    DATEDIFF(p.fecha_devolucion_esperada, '$fecha_hoy') AS DiasRestantes
                FROM prestamos p
                JOIN libros l ON p.id_libro = l.id_libro
                LEFT JOIN alumnos a ON p.id_alumnos = a.id_Alumnos
                LEFT JOIN profesores prof ON p.id_profesores = prof.id_profesores
                WHERE p.fecha_devolucion_real IS NULL 
                ORDER BY p.fecha_devolucion_esperada ASC 
                LIMIT 5";

        $res_alertas = $conexion->query($sql_alertas);

        if ($res_alertas && $res_alertas->num_rows > 0) {
            while($alerta = $res_alertas->fetch_assoc()) {
                $dias = (int)$alerta['DiasRestantes'];
                
                // Semáforo inteligente basado en los días restantes
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
                <!-- Tarjeta de Alerta Estilizada -->
                <div class="tarjeta-aviso <?php echo $clase_aviso; ?>" style="padding: 12px; border-radius: 8px; margin-bottom: 10px; border-left: 5px solid; font-size: 13px; line-height: 1.4;">
                    <span style="display: block; font-size: 11px; font-weight: bold; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 3px;">
                        <?php echo $texto_tiempo; ?>
                    </span>
                    <strong style="display: block; color: #1E293B; font-size: 14px;"><?php echo htmlspecialchars($alerta['Persona']); ?></strong>
                    <span style="display: block; color: #475569;">Libro: <i><?php echo htmlspecialchars($alerta['Libro']); ?></i></span>
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
</body>
</html>