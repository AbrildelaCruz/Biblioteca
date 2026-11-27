<?php
session_start();
if (!isset($_SESSION['admin_autenticado']) || $_SESSION['admin_autenticado'] !== true) {
    header("Location: login.php");
    exit();
}
require_once 'conexion.php';

date_default_timezone_set('America/Mexico_City');

// ==========================================
// FILTRO DE PERIODO (todo, mes, trimestre, anio)
// ==========================================
$periodo = $_GET['periodo'] ?? 'todo';
$periodos_validos = ['todo', 'mes', 'trimestre', 'anio'];
if (!in_array($periodo, $periodos_validos, true)) {
    $periodo = 'todo';
}

$filtro_fecha = "";
$titulo_periodo = "Histórico Completo";

if ($periodo === 'mes') {
    $filtro_fecha = " AND p.fecha_salida >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)";
    $titulo_periodo = "Últimos 30 Días";
} elseif ($periodo === 'trimestre') {
    $filtro_fecha = " AND p.fecha_salida >= DATE_SUB(CURDATE(), INTERVAL 90 DAY)";
    $titulo_periodo = "Últimos 3 Meses";
} elseif ($periodo === 'anio') {
    $filtro_fecha = " AND YEAR(p.fecha_salida) = YEAR(CURDATE())";
    $titulo_periodo = "Año " . date('Y');
}

// ==========================================
// 1. INDICADORES GENERALES (KPIs)
// ==========================================
$sql_kpis = "SELECT 
                COUNT(*) AS total_movimientos,
                SUM(CASE WHEN p.fecha_devolucion_real IS NOT NULL AND IFNULL(p.estado_libro, '') != 'Perdido' THEN 1 ELSE 0 END) AS total_devueltos,
                SUM(CASE WHEN p.fecha_devolucion_real IS NULL THEN 1 ELSE 0 END) AS total_activos
             FROM prestamos p
             WHERE 1=1 $filtro_fecha";
$kpis = $conexion->query($sql_kpis)->fetch_assoc();

$total_mov = (int)($kpis['total_movimientos'] ?? 0);
$total_dev = (int)($kpis['total_devueltos'] ?? 0);
$total_act = (int)($kpis['total_activos'] ?? 0);
$tasa_devolucion = ($total_mov > 0) ? round(($total_dev / $total_mov) * 100, 1) : 0;

// ==========================================
// 2. TOP 5 ALUMNAS MÁS LECTORAS
// ==========================================
$sql_top_alumnas = "SELECT 
                        IFNULL(a.Matricula, 'S/M') AS matricula,
                        TRIM(CONCAT_WS(' ', a.Nombre, a.Apellido_Paterno, a.Apellido_Materno)) AS nombre,
                        IFNULL(NULLIF(a.Grupo, ''), '-') AS grupo,
                        COUNT(p.id_prestamo) AS total_libros
                    FROM prestamos p
                    INNER JOIN alumnos a ON p.id_alumnos = a.id_Alumnos
                    WHERE p.id_alumnos IS NOT NULL $filtro_fecha
                    GROUP BY a.id_Alumnos
                    ORDER BY total_libros DESC, nombre ASC
                    LIMIT 5";
$res_top_alumnas = $conexion->query($sql_top_alumnas);

// ==========================================
// 3. TOP 5 PROFESORES MÁS LECTORES
// ==========================================
$sql_top_prof = "SELECT 
                    TRIM(CONCAT_WS(' ', prof.Nombre, prof.Apellido_Paterno, prof.Apellido_Materno)) AS nombre,
                    IFNULL(NULLIF(prof.Materia, ''), '-') AS materia,
                    COUNT(p.id_prestamo) AS total_libros
                 FROM prestamos p
                 INNER JOIN profesores prof ON p.id_profesores = prof.id_profesores
                 WHERE p.id_profesores IS NOT NULL $filtro_fecha
                 GROUP BY prof.id_profesores
                 ORDER BY total_libros DESC, nombre ASC
                 LIMIT 5";
$res_top_prof = $conexion->query($sql_top_prof);

// ==========================================
// 4. TOP 5 LIBROS MÁS LEÍDOS
// ==========================================
$sql_top_libros = "SELECT 
                        l.Titulo,
                        IFNULL(l.Autor, 'Sin autor') AS Autor,
                        COUNT(p.id_prestamo) AS veces_leido
                   FROM prestamos p
                   INNER JOIN libros l ON p.id_libro = l.id_libro
                   WHERE 1=1 $filtro_fecha
                   GROUP BY l.id_libro
                   ORDER BY veces_leido DESC, l.Titulo ASC
                   LIMIT 5";
$res_top_libros = $conexion->query($sql_top_libros);

// ==========================================
// 5. PARTICIPACIÓN POR GRUPO Y PISO
// ==========================================
$sql_grupos = "SELECT 
                    IFNULL(NULLIF(a.Grupo, ''), 'Sin grupo') AS grupo,
                    IFNULL(NULLIF(a.Piso, ''), '-') AS piso,
                    COUNT(p.id_prestamo) AS total_prestamos
               FROM prestamos p
               INNER JOIN alumnos a ON p.id_alumnos = a.id_Alumnos
               WHERE p.id_alumnos IS NOT NULL $filtro_fecha
               GROUP BY a.Grupo, a.Piso
               ORDER BY total_prestamos DESC
               LIMIT 5";
$res_grupos = $conexion->query($sql_grupos);

// ==========================================
// 6. DATOS PARA GRÁFICA: CATEGORÍAS MÁS LEÍDAS
// ==========================================
$sql_categorias = "SELECT 
                        IFNULL(l.Categoria, 'Novela y Narrativa') AS categoria,
                        COUNT(p.id_prestamo) AS total
                   FROM prestamos p
                   INNER JOIN libros l ON p.id_libro = l.id_libro
                   WHERE 1=1 $filtro_fecha
                   GROUP BY categoria
                   ORDER BY total DESC";
$res_cat = $conexion->query($sql_categorias);

$labels_cat = [];
$datos_cat = [];
if ($res_cat) {
    while ($row = $res_cat->fetch_assoc()) {
        $labels_cat[] = $row['categoria'];
        $datos_cat[] = (int)$row['total'];
    }
}

// ==========================================
// 7. DATOS PARA GRÁFICA: ALUMNAS VS PROFESORES
// ==========================================
$sql_roles = "SELECT 
                SUM(CASE WHEN p.id_alumnos IS NOT NULL THEN 1 ELSE 0 END) AS alumnos,
                SUM(CASE WHEN p.id_profesores IS NOT NULL THEN 1 ELSE 0 END) AS profesores
              FROM prestamos p
              WHERE 1=1 $filtro_fecha";
$roles_data = $conexion->query($sql_roles)->fetch_assoc();
$cant_alumnos = (int)($roles_data['alumnos'] ?? 0);
$cant_profesores = (int)($roles_data['profesores'] ?? 0);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Estadísticas de Lectura - Biblioteca Sullivan</title>
    <link rel="stylesheet" href="style.css?v=<?php echo time(); ?>">
    <link rel="icon" type="image/png" href="logobiblioteca.png">
    <script src="https://jsdelivr.net"></script>
</head>
<body class="body2">

    <div class="contenedor-estadisticas">

        <div class="barra-superior-est">
            <div>
                <h2 class="titulo-estadisticas">📊 Estadísticas e Indicadores de Lectura</h2>
                <p class="subtitulo-estadisticas">
                    Periodo analizado: <strong><?php echo $titulo_periodo; ?></strong> • Biblioteca Sullivan
                </p>
            </div>

            <div class="filtros-periodo">
                <a href="estadisticas.php?periodo=todo" class="btn-periodo <?php echo $periodo === 'todo' ? 'activo' : ''; ?>">📂 Todo</a>
                <a href="estadisticas.php?periodo=mes" class="btn-periodo <?php echo $periodo === 'mes' ? 'activo' : ''; ?>">📅 Últimos 30 días</a>
                <a href="estadisticas.php?periodo=trimestre" class="btn-periodo <?php echo $periodo === 'trimestre' ? 'activo' : ''; ?>">🗓️ 3 Meses</a>
                <a href="estadisticas.php?periodo=anio" class="btn-periodo <?php echo $periodo === 'anio' ? 'activo' : ''; ?>">📆 Año <?php echo date('Y'); ?></a>
            </div>

            <button type="button" class="btn-imprimir" onclick="window.print()">🖨️ Imprimir Reporte</button>
        </div>

        <div class="grid-rankings">
            <div class="panel-ranking">
                <h3>🏅 Top 5 Alumnas Más Lectoras</h3>
                <table class="tabla-mini">
                    <thead>
                        <tr>
                            <th class="centrado">#</th>
                            <th>Matrícula</th>
                            <th>Alumna</th>
                            <th>Grupo</th>
                            <th class="centrado">Libros</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($res_top_alumnas && $res_top_alumnas->num_rows > 0):
                            $pos = 1;
                            $medallas = [1 => '🥇', 2 => '🥈', 3 => '🥉'];
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
                <h3>📚 Top 5 Libros Más Leídos</h3>
                <table class="tabla-mini">
                    <thead>
                        <tr>
                            <th class="centrado">#</th>
                            <th>Título del Libro</th>
                            <th>Autor</th>
                            <th class="centrado">Lecturas</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($res_top_libros && $res_top_libros->num_rows > 0):
                            $pos = 1;
                            $medallas = [1 => '🥇', 2 => '🥈', 3 => '🥉'];
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
                <h3>👩‍🏫 Top 5 Profesores Más Lectores</h3>
                <table class="tabla-mini">
                    <thead>
                        <tr>
                            <th class="centrado">#</th>
                            <th>Docente</th>
                            <th>Materia</th>
                            <th class="centrado">Libros</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($res_top_prof && $res_top_prof->num_rows > 0):
                            $pos = 1;
                            $medallas = [1 => '🥇', 2 => '🥈', 3 => '🥉'];
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
    </div>

    <div class="z1">
        <a class="z2" href="registro.php">REGRESAR</a>
    </div>

    <script>
        const ctxCat = document.getElementById('graficaCategorias').getContext('2d');
        new Chart(ctxCat, {
            type: 'bar',
            data: {
                labels: <?php echo json_encode($labels_cat); ?>,
                datasets: [{
                    label: 'Libros prestados',
                    data: <?php echo json_encode($datos_cat); ?>,
                    backgroundColor: '#0F4C81',
                    borderRadius: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: { beginAtZero: true, ticks: { stepSize: 1 } }
                }
            }
        });

        const ctxUser = document.getElementById('graficaUsuarios').getContext('2d');
        new Chart(ctxUser, {
            type: 'doughnut',
            data: {
                labels: ['Alumnas', 'Profesores'],
                datasets: [{
                    data: [<?php echo $cant_alumnos; ?>, <?php echo $cant_profesores; ?>],
                    backgroundColor: ['#2A9D8F', '#0F4C81'],
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom' }
                }
            }
        });
    </script>
</body>
</html>