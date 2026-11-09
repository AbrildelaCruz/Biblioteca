<?php
// 1. Incluimos tu archivo de conexión real
require_once 'conexion.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$alertas = [];

// 2. Consulta SQL avanzada para monitorear las fechas de los préstamos
// Trae el nombre del alumno, el título del libro y calcula los días de retraso
$sql = "SELECT 
            p.id_prestamo,
            a.Nombre AS Alumno,
            l.Titulo AS Libro,
            p.fecha_devolucion,
            DATEDIFF(p.fecha_devolucion, CURDATE()) AS dias_diferencia
        FROM prestamos p
        JOIN alumnos a ON p.id_alumno = a.id_Alumnos
        JOIN libros l ON p.id_libro = l.id_libro
        WHERE p.estado = 'activo' OR p.estado = 'prestado'
        ORDER BY dias_diferencia ASC";

$result = $conexion->query($sql);

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $dias = (int)$row['dias_diferencia'];
        $clasecss = '';
        $texto = '';

        if ($dias < 0) {
            // 🟥 Caso 1: Ya se pasó de la fecha (Retrasado)
            $clasecss = 'entrega-vencida';
            $texto = "⚠️ <b>" . htmlspecialchars($row['Alumno']) . "</b> tiene un retraso de " . abs($dias) . " días con el libro: <i>" . htmlspecialchars($row['Libro']) . "</i>";
        } elseif ($dias === 0) {
            // 🟨 Caso 2: Se entrega hoy
            $clasecss = 'entrega-hoy';
            $texto = "⏳ <b>" . htmlspecialchars($row['Alumno']) . "</b> debe entregar HOY el libro: <i>" . htmlspecialchars($row['Libro']) . "</i>";
        } elseif ($dias <= 3) {
            // 🟩 Caso 3: Próximas entregas (A tiempo, menos de 3 días)
            $clasecss = 'entrega-proxima';
            $texto = "📅 <b>" . htmlspecialchars($row['Alumno']) . "</b> entrega en " . $dias . " días el libro: <i>" . htmlspecialchars($row['Libro']) . "</i>";
        }

        // Si entra en alguna categoría, lo guardamos para el panel
        if ($clasecss !== '') {
            $alertas[] = [
                'clase' => $clasecss,
                'texto' => $texto
            ];
        }
    }
}

// 3. Renderizamos las alertas de forma limpia para que tu registro.php las lea por AJAX
if (!empty($alertas)) {
    echo "<div class='lista-alertas'>";
    foreach ($alertas as $alerta) {
        echo "<div class='tarjeta-aviso " . $alerta['clase'] . "'>";
        echo "<span>" . $alerta['texto'] . "</span>";
        echo "</div>";
    }
    echo "</div>";
} else {
    // Si todo está al corriente, muestra tu letrerodashed institucional
    echo "<div class='aviso-vacio'>✨ Todo está al corriente. No hay préstamos vencidos ni alertas pendientes hoy.</div>";
}
?>