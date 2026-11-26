<?php
session_start();
if (!isset($_SESSION['admin_autenticado']) || $_SESSION['admin_autenticado'] !== true) {
    header("Location: login.php");
    exit();
}
require_once 'conexion.php';

date_default_timezone_set('America/Mexico_City');
$fecha_hoy = date('Y-m-d');
$fecha_hoy_timestamp = strtotime($fecha_hoy);

$query = trim($_GET['query'] ?? '');
$filtro_actual = $_GET['filtro'] ?? 'todos';

$condiciones = [];
if ($filtro_actual === 'pendientes') {
    $condiciones[] = "p.fecha_devolucion_real IS NULL";
} elseif ($filtro_actual === 'apartados') {
    $condiciones[] = "p.tipo_movimiento = 'Apartado' AND p.fecha_devolucion_real IS NULL";
} elseif ($filtro_actual === 'vencidos') {
    $condiciones[] = "p.fecha_devolucion_real IS NULL AND p.fecha_devolucion_esperada < '$fecha_hoy'";
} elseif ($filtro_actual === 'devueltos') {
    $condiciones[] = "p.fecha_devolucion_real IS NOT NULL";
}

$params = [];
$tipos = "";

if ($query !== '') {
    $condiciones[] = "(l.Titulo LIKE ? 
                       OR CONCAT_WS(' ', a.Nombre, a.Apellido_Paterno, a.Apellido_Materno) LIKE ? 
                       OR CONCAT_WS(' ', prof.Nombre, prof.Apellido_Paterno, prof.Apellido_Materno) LIKE ?
                       OR a.Matricula LIKE ?
                       OR prof.Matricula LIKE ?)";
    $busq = "%" . $query . "%";
    array_push($params, $busq, $busq, $busq, $busq, $busq);
    $tipos .= "sssss";
}

$where_sql = !empty($condiciones) ? "WHERE " . implode(" AND ", $condiciones) : "";

$sql = "SELECT 
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
        $where_sql
        ORDER BY p.id_prestamo DESC
        LIMIT 50";

if (!empty($params)) {
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param($tipos, ...$params);
    $stmt->execute();
    $res = $stmt->get_result();
} else {
    $res = $conexion->query($sql);
}

if ($res && $res->num_rows > 0) {
    $contador = $res->num_rows;
    while ($fila = $res->fetch_assoc()) {
        $entregado = ($fila['fecha_devolucion_real'] !== null);
        $es_apartado = ($fila['tipo_movimiento'] === 'Apartado');
        $es_perdido = ($fila['estado_libro'] === 'Perdido');
        $veces_renovado = (int)($fila['veces_renovado'] ?? 0);

        $clase_fila = '';
        if (!$entregado) {
            $dias_restantes = round((strtotime($fila['fecha_devolucion_esperada']) - $fecha_hoy_timestamp) / 86400);
            if ($dias_restantes < 0) $clase_fila = 'entrega-vencida';
            elseif ($dias_restantes == 0) $clase_fila = 'entrega-hoy';
            else $clase_fila = 'entrega-proxima';
        }

        echo "<tr class='{$clase_fila}'>";
        echo "<td style='font-weight: bold; text-align: center;'>{$contador}</td>";
        echo "<td><b>" . htmlspecialchars($fila['Libro']) . "</b></td>";
        echo "<td><u>" . htmlspecialchars($fila['Persona']) . "</u></td>";
        echo "<td>{$fila['TipoPersona']}</td>";
        $bg = $es_apartado ? '#FFFBEB' : '#EFF6FF';
        $col = $es_apartado ? '#92400E' : '#1E40AF';
        echo "<td><span style='padding: 2px 6px; border-radius: 3px; font-size: 11px; font-weight: bold; background-color: {$bg}; color: {$col};'>" . htmlspecialchars($fila['tipo_movimiento'] ?? 'Prestado') . "</span></td>";
        echo "<td>" . date('d/m/Y', strtotime($fila['fecha_salida'])) . "</td>";
        echo "<td><strong>" . date('d/m/Y', strtotime($fila['fecha_devolucion_esperada'])) . "</strong>";
        if ($veces_renovado > 0) {
            $txt_vez = ($veces_renovado === 1) ? 'vez' : 'veces';
            echo "<span class='badge-renovaciones'>🔄 Renovado: {$veces_renovado} {$txt_vez}</span>";
        } else {
            echo "<span class='sin-renovaciones'>Sin renovaciones</span>";
        }
        echo "</td><td>";
        if ($entregado) {
            if ($es_perdido) {
                echo "<span style='color: #DC3545; font-weight: bold;'>❌ Perdido (" . date('d/m/Y', strtotime($fila['fecha_devolucion_real'])) . ")</span>";
            } else {
                echo "<span style='color: green; font-weight: bold;'>✔ " . date('d/m/Y', strtotime($fila['fecha_devolucion_real'])) . "</span>";
            }
        } else {
            echo "<span style='color: #475569; font-style: italic; font-weight: bold;'>Pendiente</span>";
        }
        echo "</td><td>";
        if (!$entregado) {
            $f_url = urlencode($filtro_actual);
            $id_p = (int)$fila['id_prestamo'];
            if ($es_apartado) {
                echo "<a href='prestamos.php?filtro={$f_url}&convertir_prestamo_id={$id_p}' class='btn-accion-prestar' onclick=\"return confirm('¿El usuario ya recogió el libro apartado? Se cambiará a Préstamo activo por 9 días.');\">📖 Prestar</a>";
            } else {
                echo "<a href='prestamos.php?filtro={$f_url}&devolver_id={$id_p}' class='btn-accion-recibir' onclick=\"return confirm('¿Confirmas la entrega física del libro?');\">📥 Recibir</a>";
                echo "<a href='prestamos.php?filtro={$f_url}&renovar_id={$id_p}' class='btn-accion-renovar' onclick=\"return confirm('¿Deseas renovar este préstamo por 7 días adicionales?');\">🔄 Renovar (+7d)</a>";
            }
            echo "<a href='prestamos.php?filtro={$f_url}&perdido_id={$id_p}' class='btn-accion-perdido' onclick=\"return confirm('⚠️ ¿Seguro que deseas reportar este libro como PERDIDO permanentemente?');\">❌ Perdido</a>";
        } else {
            echo "<span class='texto-completado'>Completado</span>";
        }
        echo "</td></tr>";
        $contador--;
    }
} else {
    echo '<tr><td colspan="9" style="text-align: center; color: #991B1B; font-weight: bold; padding: 20px;">❌ No se encontraron préstamos con esa búsqueda.</td></tr>';
}
?>