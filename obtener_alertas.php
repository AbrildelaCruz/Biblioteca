<?php
// Incluimos tu archivo de conexión real
require_once 'conexion.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$alertas = [];

// 🛠️ CONSULTA CORREGIDA CON TUS LLAVES REALES: id_Alumnos
$sql = "SELECT 
            p.id_prestamo,
            a.Nombre AS Alumno,
            l.Titulo AS Libro,
            p.fecha_salida,
            DATEDIFF(DATE_ADD(p.fecha_salida, INTERVAL 7 DAY), CURDATE()) AS dias_diferencia
        FROM prestamos p
        JOIN alumnos a ON p.id_Alumnos = a.id_Alumnos
        JOIN libros l ON p.id_libro = l.id_libro
        ORDER BY dias_diferencia ASC";

$result = $conexion->query($sql);

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $dias = (int)$row['dias_diferencia'];
        $clasecss = '';
        $texto = '';

        if ($dias < 0) {
            // 🟥 Caso 1: Ya pasaron los 7 días de plazo (Retrasado)
            $clasecss = 'entrega-vencida';
            $texto = "⚠️ <b>" . htmlspecialchars($row['Alumno']) . "</b> tiene un retraso de " . abs($dias) . " días con el libro: <i>" . htmlspecialchars($row['Libro']) . "</i>";
        } elseif ($dias === 0) {
            // 🟨 Caso 2: Hoy se cumple el séptimo día
            $clasecss = 'entrega-hoy';
            $texto = "⏳ <b>" . htmlspecialchars($row['Alumno']) . "</b> debe entregar HOY el libro: <i>" . htmlspecialchars($row['Libro']) . "</i>";
        } elseif ($dias <= 3) {
            // 🟩 Caso 3: Faltan menos de 3 días para que se cumpla el plazo
            $clasecss = 'entrega-proxima';
            $texto = "📅 <b>" . htmlspecialchars($row['Alumno']) . "</b> entrega en " . $dias . " días el libro: <i>" . htmlspecialchars($row['Libro']) . "</i>";
        }

        if ($clasecss !== '') {
            $alertas[] = [
                'clase' => $clasecss,
                'texto' => $texto
            ];
        }
    }
}

// Renderizamos las alertas de forma limpia para tu contenedor de notificaciones
if (!empty($alertas)) {
    echo "<div class='lista-alertas'>";
    foreach ($alertas as $alerta) {
        echo "<div class='tarjeta-aviso " . $alerta['clase'] . "'>";
        echo "<span>" . $alerta['texto'] . "</span>";
        echo "</div>";
    }
    echo "</div>";
} else {
    // Si todo está en orden, muestra el diseño dashed limpio
    echo "<div class='aviso-vacio'>✨ Todo está al corriente. No hay préstamos vencidos ni alertas pendientes hoy.</div>";
}
?>