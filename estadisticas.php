<?php
session_start();
if (!isset($_SESSION['admin_autenticado']) || $_SESSION['admin_autenticado'] !== true) {
    header("Location: login.php");
    exit();
}
require_once 'conexion.php';

date_default_timezone_set('America/Mexico_City');

$vista = ($_GET['vista'] ?? 'general') === 'grupos' ? 'grupos' : 'general';
$anio_actual = (int)date('Y');

$anio_filtro = filter_input(INPUT_GET, 'anio', FILTER_VALIDATE_INT);
if (!$anio_filtro || $anio_filtro < 2000 || $anio_filtro > 2100) {
    $anio_filtro = 0;
}

$mes_filtro = filter_input(INPUT_GET, 'mes', FILTER_VALIDATE_INT);
if (!$mes_filtro || $mes_filtro < 1 || $mes_filtro > 12) {
    $mes_filtro = 0;
}

// Si eligió un mes pero no había elegido año, asumimos el año actual
if ($mes_filtro > 0 && $anio_filtro === 0) {
    $anio_filtro = $anio_actual;
}

$periodo = ($anio_filtro > 0 || $mes_filtro > 0) ? 'filtrado' : 'todo';

$grupo_filtro = trim($_GET['grupo'] ?? '');
$grado_filtro = trim($_GET['grado'] ?? '');
if (!in_array($grado_filtro, ['', '1', '2', '3', '4', '5'], true)) {
    $grado_filtro = '';
}

$meses_nombres = [
    1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril',
    5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto',
    9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre'
];

// Obtener automáticamente todos los años que tienen préstamos registrados + el año actual
$anios_disponibles = [$anio_actual];
$res_anios = $conexion->query("SELECT DISTINCT YEAR(fecha_salida) AS anio FROM prestamos WHERE fecha_salida IS NOT NULL ORDER BY anio DESC");
if ($res_anios) {
    while ($row_a = $res_anios->fetch_assoc()) {
        $a_val = (int)$row_a['anio'];
        if ($a_val > 0 && !in_array($a_val, $anios_disponibles, true)) {
            $anios_disponibles[] = $a_val;
        }
    }
}
rsort($anios_disponibles);

$filtro_where = "";
$titulo_periodo = "Histórico Completo";

if ($anio_filtro > 0 && $mes_filtro > 0) {
    $filtro_where = " AND YEAR(p.fecha_salida) = $anio_filtro AND MONTH(p.fecha_salida) = $mes_filtro";
    $titulo_periodo = $meses_nombres[$mes_filtro] . " " . $anio_filtro;
} elseif ($anio_filtro > 0) {
    $filtro_where = " AND YEAR(p.fecha_salida) = $anio_filtro";
    $titulo_periodo = "Ciclo / Año " . $anio_filtro;
}

if ($vista === 'general') {
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
} else {
    function obtenerGradoVilla($grupo) {
        $g = trim($grupo);
        if (preg_match('/^([1-6])\d{2}/', $g, $m)) {
            $centena = (int)$m;
            return ($centena >= 5) ? '5' : '4';
        }
        if (preg_match('/^([1-3])/', $g, $m)) {
            return $m;
        }
        return '';
    }

    $lista_grupos = [];
    $res_lg = $conexion->query("SELECT DISTINCT COALESCE(NULLIF(TRIM(Grupo), ''), 'Sin Grupo') AS grupo FROM alumnos ORDER BY grupo ASC");
    if ($res_lg) {
        while ($g = $res_lg->fetch_assoc()) {
            $ng_lista = $g['grupo'];
            $grado_lista = obtenerGradoVilla($ng_lista);
            if ($grado_filtro === '' || $grado_lista === $grado_filtro) {
                $lista_grupos[] = $ng_lista;
            }
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
        SUM(CASE WHEN p.fecha_devolucion_real IS NULL THEN 1 ELSE 0 END) AS activos
        FROM alumnos a
        INNER JOIN prestamos p ON a.id_Alumnos = p.id_alumnos
        WHERE 1=1 $filtro_where
        GROUP BY a.id_Alumnos
        ORDER BY grupo ASC, total_libros DESC, nombre ASC");

    $grupos_agrupados = [];
    if ($res_gr) {
        while ($f = $res_gr->fetch_assoc()) {
            $ng = $f['grupo'];
            $grado_actual = obtenerGradoVilla($ng);

            if ($grado_filtro !== '' && $grado_actual !== $grado_filtro) continue;
            if ($grupo_filtro !== '' && $ng !== $grupo_filtro) continue;

            if (!isset($grupos_agrupados[$ng])) {
                $grupos_agrupados[$ng] = ['total_prestamos' => 0, 'alumnas' => []];
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
</head>
<body class="body2">

    <div class="contenedor-estadisticas">

        <div class="barra-superior-est">
            <div>
                <h2 class="titulo-estadisticas">📊 Estadísticas de Lectura</h2>
                <p class="subtitulo-estadisticas">Periodo: <strong><?php echo $titulo_periodo; ?></strong> • Biblioteca Sullivan</p>
            </div>

            <div class="tabs-vista-est">
                <a href="estadisticas.php?vista=general&anio=<?php echo $anio_filtro; ?>&mes=<?php echo $mes_filtro; ?>" class="btn-vista-est <?php echo $vista === 'general' ? 'activo' : ''; ?>">📊 General</a>
                <a href="estadisticas.php?vista=grupos&anio=<?php echo $anio_filtro; ?>&mes=<?php echo $mes_filtro; ?>" class="btn-vista-est <?php echo $vista === 'grupos' ? 'activo' : ''; ?>">🏫 Grupos</a>
            </div>

            <div class="filtros-periodo">
                <a href="estadisticas.php?vista=<?php echo $vista; ?>&grado=<?php echo urlencode($grado_filtro); ?>&grupo=<?php echo urlencode($grupo_filtro); ?>" class="btn-periodo <?php echo $periodo === 'todo' ? 'activo' : ''; ?>">📂 Todo</a>

                <form method="GET" action="estadisticas.php" class="filtros-periodo">
                    <input type="hidden" name="vista" value="<?php echo htmlspecialchars($vista); ?>">
                    <input type="hidden" name="grado" value="<?php echo htmlspecialchars($grado_filtro); ?>">
                    <input type="hidden" name="grupo" value="<?php echo htmlspecialchars($grupo_filtro); ?>">

                    <select name="anio" class="select-grupo-est" onchange="this.form.submit()">
                        <option value="0">📆 Todos los años</option>
                        <?php foreach ($anios_disponibles as $a_op): ?>
                            <option value="<?php echo $a_op; ?>" <?php echo ($anio_filtro === $a_op) ? 'selected' : ''; ?>>
                                Año <?php echo $a_op; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>

                    <select name="mes" class="select-grupo-est" onchange="this.form.submit()">
                        <option value="0">📅 Todos los meses</option>
                        <?php foreach ($meses_nombres as $num_m => $nombre_m): ?>
                            <option value="<?php echo $num_m; ?>" <?php echo ($mes_filtro === $num_m) ? 'selected' : ''; ?>>
                                <?php echo $nombre_m; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </form>
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
                    <h3>👩‍🏫 Profesores Más Lectores</h3>
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

            <?php
            $grados_villa = [
                '1' => ['titulo' => '1° Grado', 'nivel' => 'Secundaria'],
                '2' => ['titulo' => '2° Grado', 'nivel' => 'Secundaria'],
                '3' => ['titulo' => '3° Grado', 'nivel' => 'Secundaria'],
                '4' => ['titulo' => '4° Grado', 'nivel' => 'Bachillerato'],
                '5' => ['titulo' => '5° Grado', 'nivel' => 'Bachillerato'],
            ];
            ?>

            <div class="barra-grados-est">
                <a href="estadisticas.php?vista=grupos&anio=<?php echo $anio_filtro; ?>&mes=<?php echo $mes_filtro; ?>"
                   class="ficha-grado-est <?php echo $grado_filtro === '' ? 'activa' : ''; ?>">
                    🏫 Todos los Grados
                </a>
                <?php foreach ($grados_villa as $num_g => $datos_g): ?>
                    <a href="estadisticas.php?vista=grupos&anio=<?php echo $anio_filtro; ?>&mes=<?php echo $mes_filtro; ?>&grado=<?php echo $num_g; ?>"
                       class="ficha-grado-est <?php echo $grado_filtro === $num_g ? 'activa' : ''; ?>">
                        🎓 <?php echo $datos_g['titulo']; ?>
                        <span class="nivel-grado-tag">(<?php echo $datos_g['nivel']; ?>)</span>
                    </a>
                <?php endforeach; ?>
            </div>

            <div class="barra-filtro-grupo-inline">
                <span class="label-filtro-grupo">
                    <?php echo ($grado_filtro !== '') ? "🎓 Grupos de {$grados_villa[$grado_filtro]['titulo']} ({$grados_villa[$grado_filtro]['nivel']})" : "🏫 Desglose individual por Grupo y Alumna"; ?>
                </span>
                <form method="GET" action="estadisticas.php">
                    <input type="hidden" name="vista" value="grupos">
                    <input type="hidden" name="anio" value="<?php echo $anio_filtro; ?>">
                    <input type="hidden" name="mes" value="<?php echo $mes_filtro; ?>">
                    <input type="hidden" name="grado" value="<?php echo htmlspecialchars($grado_filtro); ?>">
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
                                            $icono_pos = $medallas[$pos] ?? $pos;
                                        ?>
                                            <tr>
                                                <td class="medalla"><?php echo $icono_pos; ?></td>
                                                <td><small class="matricula-est"><?php echo htmlspecialchars($al['matricula']); ?></small></td>
                                                <td><strong><?php echo htmlspecialchars($al['nombre']); ?></strong></td>
                                                <td><?php echo htmlspecialchars($al['familia']); ?></td>
                                                <td class="centrado"><?php echo htmlspecialchars($al['piso']); ?></td>
                                                <td class="centrado"><span class="badge-total-lecturas"><?php echo $total_al; ?></span></td>
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
                <div class="panel-ranking"><p class="texto-vacio-est">No hay lecturas registradas en este grado o periodo.</p></div>
            <?php endif; ?>

        <?php endif; ?>

    </div>

    <div class="z1">
        <a class="z2" href="registro.php">REGRESAR</a>
    </div>

</body>
</html>