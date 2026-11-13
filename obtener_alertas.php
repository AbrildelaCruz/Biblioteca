<?php
require_once 'conexion.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$alertas = [];

// Solo busca préstamos que AÚN NO tienen fecha de devolución real (fecha_devolucion_real IS NULL)
$sql = "SELECT 
            p.id_prestamo,
            COALESCE(a.Nombre, prof.Nombre, 'Usuario') AS Persona,
            CASE WHEN a.Nombre IS NOT NULL THEN 'Alumno' ELSE 'Profesor' END AS Rol,
            l.Titulo AS Libro,
            p.fecha_salida,
            p.fecha_devolucion_esperada,
            DATEDIFF(p.fecha_devolucion_esperada, CURDATE()) AS dias_diferencia
        FROM prestamos p
        LEFT JOIN alumnos a ON p.id_alumnos = a.id_Alumnos
        LEFT JOIN profesores prof ON p.id_profesores = prof.id_profesores
        JOIN libros l ON p.id_libro = l.id_libro
        WHERE p.fecha_devolucion_real IS NULL
        ORDER BY dias_diferencia ASC";

$result = $conexion->query($sql);

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $dias = (int)$row['dias_diferencia'];
        $clasecss = '';
        $texto = '';
        $etiquetaRol = $row['Rol'];

        if ($dias < 0) {
            $clasecss = 'entrega-vencida';
            $texto = "⚠️ <b>[" . $etiquetaRol . "] " . htmlspecialchars($row['Persona']) . "</b> tiene un retraso de " . abs($dias) . " días con el libro: <i>" . htmlspecialchars($row['Libro']) . "</i>";
        } elseif ($dias === 0) {
            $clasecss = 'entrega-hoy';
            $texto = "⏳ <b>[" . $etiquetaRol . "] " . htmlspecialchars($row['Persona']) . "</b> debe entregar HOY el libro: <i>" . htmlspecialchars($row['Libro']) . "</i>";
        } elseif ($dias <= 3) {
            $clasecss = 'entrega-proxima';
            $texto = "📅 <b>[" . $etiquetaRol . "] " . htmlspecialchars($row['Persona']) . "</b> entrega en " . $dias . " días el libro: <i>" . htmlspecialchars($row['Libro']) . "</i>";
        }

        if ($clasecss !== '') {
            $alertas[] = [
                'clase' => $clasecss,
                'texto' => $texto
            ];
        }
    }
}

echo "<h3 style='margin-top: 0; color: #0F4C81; font-size: 16px; border-bottom: 2px solid #E8EEF5; padding-bottom: 10px; display: flex; align-items: center; gap: 8px; text-transform: uppercase; letter-spacing: 0.5px;'>🔔 Alertas de Devolución</h3>";

if (!empty($alertas)) {
    echo "<div class='lista-alertas'>";
    foreach ($alertas as $alerta) {
        echo "<div class='tarjeta-aviso " . $alerta['clase'] . "' style='padding: 12px; border-radius: 8px; margin-bottom: 10px; border-left: 5px solid; font-size: 13px; line-height: 1.4;'>";
        echo "<span>" . $alerta['texto'] . "</span>";
        echo "</div>";
    }
    echo "</div>";
} else {
    echo "<div class='aviso-vacio'>✨ Todo está al corriente. No hay préstamos vencidos ni alertas pendientes hoy.</div>";
}
?>