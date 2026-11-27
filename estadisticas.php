<?php
session_start();
if (!isset($_SESSION['admin_autenticado']) || $_SESSION['admin_autenticado'] !== true) {
    header("Location: login.php");
    exit();
}
require_once 'conexion.php';

date_default_timezone_set('America/Mexico_City');

$vista = ($_GET['vista'] ?? 'general') === 'grupos' ? 'grupos' : 'general';
$periodo = $_GET['periodo'] ?? 'todo';
if (!in_array($periodo, ['todo', 'mes', 'trimestre', 'anio'], true)) {
    $periodo = 'todo';
}
$grupo_filtro = trim($_GET['grupo'] ?? '');

$filtro_where = "";
$filtro_on = "";
$titulo_periodo = "Histórico Completo";

if ($periodo === 'mes') {
    $filtro_where = " AND p.fecha_salida >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)";
    $filtro_on = $filtro_where;
    $titulo_periodo = "Últimos 30 Días";
} elseif ($periodo === 'trimestre') {
    $filtro_where = " AND p.fecha_salida >= DATE_SUB(CURDATE(), INTERVAL 90 DAY)";
    $filtro_on = $filtro_where;
    $titulo_periodo = "Últimos 3 Meses";
} elseif ($periodo === 'anio') {
    $filtro_where = " AND YEAR(p.fecha_salida) = YEAR(CURDATE())";
    $filtro_on = $filtro_where;
    $titulo_periodo = "Año " . date('Y');
}

if ($vista === 'general') {
    $kpis = $conexion->query("SELECT 
        COUNT(*) AS total_mov,
        SUM(CASE WHEN p.fecha_devolucion_real IS NOT NULL AND IFNULL(p.estado_libro, '') != 'Perdido' THEN 1 ELSE 0 END) AS total_dev,
        SUM(CASE WHEN p.fecha_devolucion_real IS NULL THEN 1 ELSE 0 END) AS total_act
        FROM prestamos p WHERE 1=1 $filtro_where")->fetch_assoc();

    $total_mov = (int)($kpis['total_mov'] ?? 0);
    $total_dev = (int)($kpis['total_dev'] ?? 0);
    $total_act = (int)($kpis['total_act'] ?? 0);
    $tasa_dev = ($total_mov > 0) ? round(($total_dev / $total_mov) * 100, 1) : 0;

    $res_top_alumnas = $conexion->query("SELECT 
        IFNULL(a.Matricula, 'S/M') AS matricula,
        TRIM(CONCAT_WS(' ', a.Nombre, a.Apellido_Paterno, a.Apellido_Materno)) AS nombre,
        IFNULL(NULLIF(a.Grupo, ''), '-') AS grupo,
        COUNT(p.id_prestamo) AS total_libros
        FROM prestamos p INNER JOIN alumnos a ON p.id_alumnos = a.id_Alumnos
        WHERE p.id_alumnos IS NOT NULL $filtro_where
        GROUP BY a.id_Alumnos ORDER BY total_libros DESC, nombre ASC LIMIT 5");

    $res_top_prof = $conexion->query("SELECT 
        TRIM(CONCAT_WS(' ', prof.Nombre, prof.Apellido_Paterno, prof.Apellido_Materno)) AS nombre,
        IFNULL(NULLIF(prof.Materia, ''), '-') AS materia,
        COUNT(p.id_prestamo) AS total_libros
        FROM prestamos p INNER JOIN profesores prof ON p.id_profesores = prof.id_profesores
        WHERE p.id_profesores IS NOT NULL $filtro_where
        GROUP BY prof.id_profesores ORDER BY total_libros DESC, nombre ASC LIMIT 5");

    $res_top_libros = $conexion->query("SELECT 
        l.Titulo, IFNULL(l.Autor, 'Sin autor') AS Autor, COUNT(p.id_prestamo) AS veces_leido
        FROM prestamos p INNER JOIN libros l ON p.id_libro = l.id_libro
        WHERE 1=1 $filtro_where
        GROUP BY l.id_libro ORDER BY veces_leido DESC, l.Titulo ASC LIMIT 5");

    $res_grupos_top = $conexion->query("SELECT 
        IFNULL(NULLIF(a.Grupo, ''), 'Sin grupo') AS grupo,
        IFNULL(NULLIF(a.Piso, ''), '-') AS piso,
        COUNT(p.id_prestamo) AS total_prestamos
        FROM prestamos p INNER JOIN alumnos a ON p.id_alumnos = a.id_Alumnos
        WHERE p.id_alumnos IS NOT NULL $filtro_where
        GROUP BY a.Grupo, a.Piso ORDER BY total_prestamos DESC LIMIT 5");

    $res_cat = $conexion->query("SELECT IFNULL(l.Categoria, 'Novela y Narrativa') AS categoria, COUNT(p.id_prestamo) AS total
        FROM prestamos p INNER JOIN libros l ON p.id_libro = l.id_libro
        WHERE 1=1 $filtro_where GROUP BY categoria ORDER BY total DESC");
    $labels_cat = [];
    $datos_cat = [];
    if ($res_cat) {
        while ($r = $res_cat->fetch_assoc()) {
            $labels_cat[] = $r['categoria'];
            $datos_cat[] = (int)$r['total'];
        }
    }

    $roles_data = $conexion->query("SELECT 
        SUM(CASE WHEN p.id_alumnos IS NOT NULL THEN 1 ELSE 0 END) AS alumnos,
        SUM(CASE WHEN p.id_profesores IS NOT NULL THEN 1 ELSE 0 END) AS profesores
        FROM prestamos p WHERE 1=1 $filtro_where")->fetch_assoc();
    $cant_alumnos = (int)($roles_data['alumnos'] ?? 0);
    $cant_profesores = (int)($roles_data['profesores'] ?? 0);
} else {
    $lista_grupos = [];
    $res_lg = $conexion->query("SELECT DISTINCT COALESCE(NULLIF(TRIM(Grupo), ''), 'Sin Grupo') AS grupo FROM alumnos ORDER BY grupo ASC");
    if ($res_lg) {
        while ($g = $res_lg->fetch_assoc()) {
            $lista_grupos[] = $g['grupo'];
        }
    }

    $res_gr = $conexion->query("SELECT 
        COALESCE(NULLIF(TRIM(a.Grupo), ''), 'Sin Grupo') AS grupo,
        IFNULL(NULLIF(TRIM(a.Piso), ''), '-') AS piso,
        IFNULL(a.Matricula, 'S/M') AS matricula,
        TRIM(CONCAT_WS(' ', a.Nombre, a.Apellido_Paterno, a.Apellido_Materno)) AS nombre,
        IFNULL(NULLIF(TRIM(a.Familia), ''), '-') AS familia,
        COUNT(p.id_prestamo) AS total_libros,
        SUM(CASE WHEN p.fecha_devolucion_real IS NOT NULL AND IFNULL(p.estado_libro, '') != 'Perdido' THEN 1 ELSE 0 END) AS devueltos,
        SUM(CASE WHEN p.fecha_devolucion_real IS NULL AND p.id_prestamo IS NOT NULL THEN 1 ELSE 0 END) AS activos
        FROM alumnos a
        LEFT JOIN prestamos p ON a.id_Alumnos = p.id_alumnos $filtro_on
        GROUP BY a.id_Alumnos
        ORDER BY grupo ASC, total_libros DESC, nombre ASC");

    $grupos_agrupados = [];
    if ($res_gr) {
        while ($f = $res_gr->fetch_assoc()) {
            $ng = $f['grupo'];
            if ($grupo_filtro !== '' && $ng !== $grupo_filtro) continue;
            if (!isset($grupos_agrupados[$ng])) {
                $grupos_agrupados[$ng] = ['piso' => $f['piso'], 'total_prestamos' => 0, 'alumnas' => []];
            }
            $grupos_agrupados[$ng]['total_prestamos'] += (int)$f['total_libros'];
            $grupos_agrupados[$ng]['alumnas'][] = $f;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Estadísticas de Lectura - Biblioteca Sullivan</title>
    <link rel="stylesheet" href="style.css?v=<?php echo time(); ?>">
    <link rel="icon" type="image/png" href="logobiblioteca.png">
    <?php if ($vista === 'general'): ?>
        <script src="https://jsdelivr.net"></script>
    <?php endif; ?>
</head>
<body class="body2">

    <div class="contenedor-estadisticas">

        <div class="barra-superior-est">
            <div>
                <h2 class="titulo-estadisticas">📊 Estadísticas de Lectura</h2>
                <p class="subtitulo-estadisticas">Periodo: <strong><?php echo $titulo_periodo; ?></strong> • Biblioteca Sullivan</p>
            </div>

            <div class="tabs-vista-est">
                <a href="estadisticas.php?vista=general&periodo=<?php echo urlencode($periodo); ?>" class="btn-vista-est <?php echo $vista === 'general' ? 'activo' : ''; ?>">📊 General</a>
                <a href="estadisticas.php?vista=grupos&periodo=<?php echo urlencode($periodo); ?>" class="btn-vista-est <?php echo $vista === 'grupos' ? 'activo' : ''; ?>">🏫 Grupos</a>
            </div>

            <div class="filtros-periodo">
                <a href="estadisticas.php?vista=<?php echo $vista; ?>&periodo=todo&grupo=<?php echo urlencode($grupo_filtro); ?>" class="btn-periodo <?php echo $periodo === 'todo' ? 'activo' : ''; ?>">📂 Todo</a>
                <a href="estadisticas.php?vista=<?php echo $vista; ?>&periodo=mes&grupo=<?php echo urlencode($grupo_filtro); ?>" class="btn-periodo <?php echo $periodo === 'mes' ? 'activo' : ''; ?>">📅 30 días</a>
                <a href="estadisticas.php?vista=<?php echo $vista; ?>&periodo=trimestre&grupo=<?php echo urlencode($grupo_filtro); ?>" class="btn-periodo <?php echo $periodo === 'trimestre' ? 'activo' : ''; ?>">🗓️ 3 Meses</a>
                <a href="estadisticas.php?vista=<?php echo $vista; ?>&periodo=anio&grupo=<?php echo urlencode($grupo_filtro); ?>" class="btn-periodo <?php echo $periodo === 'anio' ? 'activo' : ''; ?>">📆 <?php echo date('Y'); ?></a>
            </div>

            <button type="button" class="btn-imprimir" onclick="window.print()">🖨️ Imprimir</button>
        </div>

        <?php if ($vista === 'general'): ?>


            <div class="grid-rankings">
                <div class="panel-ranking">
                    <h3>🏅 Alumnas Más Lectoras</h3>
                    <table class="tabla-mini">
                        <thead>
                            <tr><th class="centrado">#</th><th>Matrícula</th><th>Alumna</th><th>Grupo</th><th class="centrado">Libros</th></tr>
                        </thead>
                        <tbody>
                            <?php if ($res_top_alumnas && $res_top_alumnas->num_rows > 0):
                                $pos = 1; $medallas = [1 => '🥇', 2 => '🥈', 3 => '🥉'];
                                while ($al = $res_top_alumnas->fetch_assoc()): ?>
                                    <tr>
                                        <td class="medalla"><?php echo $medallas[$pos] ?? $pos; ?></td>
                                        <td><small class="matricula-est"><?php echo htmlspecialchars($al['matricula']); ?></small></td>
                                        <td><strong><?php echo htmlspecialchars($al['nombre']); ?></strong></td>
                                        <td><?php echo htmlspecialchars($al['grupo']); ?></td>
                                        <td class="centrado"><span class="badge-total-lecturas"><?php echo $al['total_libros']; ?> libros</span></td>
                                    </tr>
                                <?php $pos++; endwhile;
                            else: ?>
                                <tr><td colspan="5" class="texto-vacio-est">Sin préstamos de alumnas en este periodo.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <div class="panel-ranking">
                    <h3>📚 Libros Más Leídos</h3>
                    <table class="tabla-mini">
                        <thead>
                            <tr><th class="centrado">#</th><th>Título del Libro</th><th>Autor</th><th class="centrado">Lecturas</th></tr>
                        </thead>
                        <tbody>
                            <?php if ($res_top_libros && $res_top_libros->num_rows > 0):
                                $pos = 1; $medallas = [1 => '🥇', 2 => '🥈', 3 => '🥉'];
                                while ($lib = $res_top_libros->fetch_assoc()): ?>
                                    <tr>
                                        <td class="medalla"><?php echo $medallas[$pos] ?? $pos; ?></td>
                                        <td><strong><?php echo htmlspecialchars($lib['Titulo']); ?></strong></td>
                                        <td><?php echo htmlspecialchars($lib['Autor']); ?></td>
                                        <td class="centrado"><span class="badge-total-lecturas"><?php echo $lib['veces_leido']; ?> veces</span></td>
                                    </tr>
                                <?php $pos++; endwhile;
                            else: ?>
                                <tr><td colspan="4" class="texto-vacio-est">Sin lecturas registradas en este periodo.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <div class="panel-ranking">
                    <h3>👩‍🏫Profesores Más Lectores</h3>
                    <table class="tabla-mini">
                        <thead>
                            <tr><th class="centrado">#</th><th>Docente</th><th>Materia</th><th class="centrado">Libros</th></tr>
                        </thead>
                        <tbody>
                            <?php if ($res_top_prof && $res_top_prof->num_rows > 0):
                                $pos = 1; $medallas = [1 => '🥇', 2 => '🥈', 3 => '🥉'];
                                while ($pr = $res_top_prof->fetch_assoc()): ?>
                                    <tr>
                                        <td class="medalla"><?php echo $medallas[$pos] ?? $pos; ?></td>
                                        <td><strong><?php echo htmlspecialchars($pr['nombre']); ?></strong></td>
                                        <td><small><?php echo htmlspecialchars($pr['materia']); ?></small></td>
                                        <td class="centrado"><span class="badge-total-lecturas"><?php echo $pr['total_libros']; ?> libros</span></td>
                                    </tr>
                                <?php $pos++; endwhile;
                            else: ?>
                                <tr><td colspan="4" class="texto-vacio-est">Sin préstamos de docentes en este periodo.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        <?php else: ?>

            <div class="barra-filtro-grupo-inline">
                <span class="label-filtro-grupo">🏫 Desglose individual por Grupo y Alumna</span>
                <form method="GET" action="estadisticas.php">
                    <input type="hidden" name="vista" value="grupos">
                    <input type="hidden" name="periodo" value="<?php echo htmlspecialchars($periodo); ?>">
                    <select name="grupo" class="select-grupo-est" onchange="this.form.submit()">
                        <option value="">Mostrar todos los grupos</option>
                        <?php foreach ($lista_grupos as $grp): ?>
                            <option value="<?php echo htmlspecialchars($grp); ?>" <?php echo ($grupo_filtro === $grp) ? 'selected' : ''; ?>>
                                Grupo <?php echo htmlspecialchars($grp); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </form>
            </div>

                <?php if (!empty($grupos_agrupados)): ?>
                        <div class="grid-tarjetas-grupos">
                    <?php foreach ($grupos_agrupados as $nombre_grupo => $info_grupo): ?>
                        <div class="tarjeta-grupo-est">
                            <div class="encabezado-grupo-est">
                                <div>
                                    <h3 class="titulo-grupo-est">Grupo <?php echo htmlspecialchars($nombre_grupo); ?></h3>
                                </div>
                                <div class="resumen-badges-grupo">
                                    <span class="badge-grupo-alumnas">👩‍🎓 <?php echo count($info_grupo['alumnas']); ?> alumnas</span>
                                    <span class="badge-grupo-total">📚 <?php echo $info_grupo['total_prestamos']; ?> libros</span>
                                </div>
                            </div>

                            <div class="tabla-responsiva">
                                <table class="tabla-mini">
                                    <thead>
                                        <tr>
                                            <th class="centrado">#</th>
                                            <th>Matrícula</th>
                                            <th>Alumna</th>
                                            <th>Familia</th>
                                            <th class="centrado">Piso</th>
                                            <th class="centrado">Total</th>
                                            <th class="centrado">Devueltos</th>
                                            <th class="centrado">Activos</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                            $pos = 1;
                                            $medallas = [1 => '🥇', 2 => '🥈', 3 => '🥉'];
                                            foreach ($info_grupo['alumnas'] as $al):
                                            $total_al = (int)$al['total_libros'];
                                            $icono_pos = ($total_al > 0 && isset($medallas[$pos])) ? $medallas[$pos] : $pos;
                                        ?>
                                            <tr>
                                                <td class="medalla"><?php echo $icono_pos; ?></td>
                                                <td><small class="matricula-est"><?php echo htmlspecialchars($al['matricula']); ?></small></td>
                                                <td><strong><?php echo htmlspecialchars($al['nombre']); ?></strong></td>
                                                <td><?php echo htmlspecialchars($al['familia']); ?></td>
                                                <td class="centrado"><?php echo htmlspecialchars($al['piso']); ?></td>
                                                <td class="centrado">
                                                    <?php if ($total_al > 0): ?>
                                                        <span class="badge-total-lecturas"><?php echo $total_al; ?></span>
                                                    <?php else: ?>
                                                        <span class="texto-cero-est">0</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td class="centrado texto-devuelto-est"><?php echo (int)$al['devueltos']; ?></td>
                                                <td class="centrado texto-activo-est"><?php echo (int)$al['activos']; ?></td>
                                            </tr>
                                        <?php $pos++; endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="panel-ranking"><p class="texto-vacio-est">No hay alumnas o grupos registrados para mostrar.</p></div>
            <?php endif; ?>

        <?php endif; ?>

    </div>

    <div class="z1">
        <a class="z2" href="registro.php">REGRESAR</a>
    </div>

    <?php if ($vista === 'general'): ?>
    <script>
        new Chart(document.getElementById('graficaCategorias').getContext('2d'), {
            type: 'bar',
            data: {
                labels: <?php echo json_encode($labels_cat); ?>,
                datasets: [{ label: 'Libros prestados', data: <?php echo json_encode($datos_cat); ?>, backgroundColor: '#0F4C81', borderRadius: 6 }]
            },
            options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } } }
        });

        new Chart(document.getElementById('graficaUsuarios').getContext('2d'), {
            type: 'doughnut',
            data: {
                labels: ['Alumnas', 'Profesores'],
                datasets: [{ data: [<?php echo $cant_alumnos; ?>, <?php echo $cant_profesores; ?>], backgroundColor: ['#2A9D8F', '#0F4C81'], borderWidth: 2 }]
            },
            options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'bottom' } } }
        });
    </script>
    <?php endif; ?>
</body>
</html>