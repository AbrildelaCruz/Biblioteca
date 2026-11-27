<?php
session_start();
if (!isset($_SESSION['admin_autenticado']) || $_SESSION['admin_autenticado'] !== true) {
    header("Location: login.php");
    exit();
}
require_once 'conexion.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Control - Biblioteca Sullivan</title>
    <link rel="stylesheet" href="style.css?v=<?php echo time(); ?>">
    <link rel="icon" type="image/png" href="logobiblioteca.png">
</head>
<body class="body2">
    <div class="fondo-animado-wrapper">
        <div class="fondo-animado-img"></div>
        <div class="fondo-animado-capa"></div>

    <div class="dashboard-layout">
        
        <!-- COLUMNA IZQUIERDA: PANEL DE MÓDULOS -->
        <div class="columna-izquierda">
            <div class="contenedorpadrere" style="position: relative !important; top: 0 !important; left: 0 !important; transform: none !important; margin: 0; width: 100%; max-width: 450px;">
                <span class="Modulos">Panel de Módulos</span>
                
                <!-- CONTADORES RÁPIDOS AUTOMÁTICOS (KPIs) -->
                <div class="contenedor-kpis" style="display: flex; gap: 15px; width: 100%; max-width: 400px; margin-bottom: 20px; box-sizing: border-box;">
                    <?php
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
                    <a class="bton-1" href="registrar_usuario.php">👤 Registrar Usuarios</a>
                    <a class="bton-1" href="catalogo.php">✍️ Registrar Libros Nuevos</a>
                    <a class="bton-3" href="nuevo_prestamo.php">➕ Registrar Préstamos</a>
                    <a class="bton-4" href="ver_catalogo.php">📊 Ver Inventario Completo</a>
                </div>

                <!-- BLOQUE 2: OPERACIÓN DIARIA -->
                <div class="seccion-menu" style="width: 100%; text-align: center;">
                    <span class="subtitulo-menu">⚡ Operación de Préstamos</span>
                    <a class="bton-3" href="catalogo_interactivo.php">🟢 Catálogo Interactivo</a>
                    <a class="bton-4" href="prestamos.php">📋 Bitácora de Préstamos</a>
                    <a class="bton-4" href="retrasados.php" style="background-color: #FEE2E2 !important; color: #991B1B !important; border-color: #FCA5A5 !important;">⚠️ Entregas Retrasadas</a>
                    <a class="bton-4 bton-estadisticas" href="estadisticas.php">📊 Estadísticas</a>
            </div>
        </div>

        <!-- COLUMNA DERECHA: NOTIFICACIONES ARRIBA Y STOCK BAJO ABAJO -->
        <div class="columna-derecha-alertas">
            
            <!-- PANEL 1: NOTIFICACIONES DE DEVOLUCIÓN -->
            <div class="panel-notificaciones-derecho">
                <h3 style="margin-top: 0; color: #0F4C81; font-size: 16px; border-bottom: 2px solid #E8EEF5; padding-bottom: 10px; display: flex; align-items: center; gap: 8px; text-transform: uppercase; letter-spacing: 0.5px;">
                    🔔 Alertas de Devolución
                </h3>
                <div class="lista-alertas">
                    <div class="aviso-vacio">Cargando alertas...</div>
                </div>
            </div>

            <!-- PANEL 2: ALERTAS DE STOCK BAJO EN INVENTARIO -->
            <div class="panel-notificaciones-derecho" style="width: 100%; max-width: 100%; box-sizing: border-box;">
                <h3 style="margin-top: 0; color: #0F4C81; font-size: 16px; border-bottom: 2px solid #E8EEF5; padding-bottom: 10px; display: flex; align-items: center; gap: 8px; text-transform: uppercase; letter-spacing: 0.5px;">
                    📦 Alertas de Stock Bajo
                </h3>
                <div class="lista-alertas" style="max-height: 260px; overflow-y: auto; padding-right: 6px;">
                    <?php
                    $sql_stock = "SELECT Titulo, Autor, Ejemplares_disponibles, Ejemplares_totales 
                                  FROM libros 
                                  WHERE Ejemplares_disponibles <= 1 
                                  ORDER BY Ejemplares_disponibles ASC, Titulo ASC";
                    $res_stock = $conexion->query($sql_stock);

                    if ($res_stock && $res_stock->num_rows > 0) {
                        while ($libro_bajo = $res_stock->fetch_assoc()) {
                            $disp = (int)$libro_bajo['Ejemplares_disponibles'];
                            $totales = (int)$libro_bajo['Ejemplares_totales'];

                            if ($disp === 0) {
                                $clase_stock = "entrega-vencida";
                                $etiqueta_stock = "🚫 AGOTADO (0 DISPONIBLES)";
                            } else {
                                $clase_stock = "entrega-hoy";
                                $etiqueta_stock = "⚠️ ÚLTIMO EJEMPLAR (1 DISPONIBLE)";
                            }
                            ?>
                            <div class="tarjeta-aviso <?php echo $clase_stock; ?>" style="padding: 12px; border-radius: 8px; margin-bottom: 10px; border-left: 5px solid; font-size: 13px; line-height: 1.4;">
                                <span style="display: block; font-size: 11px; font-weight: bold; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 3px;">
                                    <?php echo $etiqueta_stock; ?>
                                </span>
                                <strong style="display: block; color: #1E293B; font-size: 14px;">
                                    <?php echo htmlspecialchars($libro_bajo['Titulo']); ?>
                                </strong>
                                <span style="display: block; color: #475569; font-size: 12px; margin-top: 2px;">
                                    Autor: <?php echo htmlspecialchars($libro_bajo['Autor'] ?? 'Sin autor'); ?> | Total en acervo: <b><?php echo $totales; ?></b>
                                </span>
                            </div>
                            <?php
                        }
                    } else {
                        echo '<div class="aviso-vacio">✅ Todos los títulos cuentan con suficiente stock disponible.</div>';
                    }
                    ?>
                </div>
            </div>

        </div>
    </div>

<div class="z1">
    <a class="z2" href="cerrar_sesion.php">CERRAR SESIÓN</a>
</div>

    <script>
    function tocarSonidoCampanita() {
        const AudioCtx = window.AudioContext || window.webkitAudioContext;
        if (!AudioCtx) return;

        const ctx = new AudioCtx();
        if (ctx.state === 'suspended') {
            ctx.resume();
        }

        const ahora = ctx.currentTime;
        const frecuencias = [1318.5, 1975.5];

        frecuencias.forEach((freq, i) => {
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

    function activarCampanita(elemento) {
        tocarSonidoCampanita();
        if (elemento) {
            elemento.classList.remove('sonar');
            void elemento.offsetWidth;
            elemento.classList.add('sonar');
        }
    }

    function cargarAlertasCampana() {
        fetch('obtener_alertas.php')
            .then(response => response.text())
            .then(html => {
                const panel = document.querySelector('.panel-notificaciones-derecho');
                if (panel) {
                panel.innerHTML = html;

                    const campanaUrgente = panel.querySelector('.campanita-icono.urgente');
                    if (campanaUrgente && !sessionStorage.getItem('alerta_campana_sonada')) {
                        activarCampanita(campanaUrgente);
                        sessionStorage.setItem('alerta_campana_sonada', '2');
                    }
                }
            })
            .catch(error => console.error('Error cargando alertas:', error));
    }

    // Respaldo automático silencioso cada 5 minutos (300,000 ms)
    function ejecutarRespaldoSilencioso() {
        fetch('auto_respaldo.php').catch(() => {});
    }

    window.onload = function() {
        cargarAlertasCampana();
        ejecutarRespaldoSilencioso();
        setInterval(ejecutarRespaldoSilencioso, 300000);
    };
    </script>

</body>
</html>