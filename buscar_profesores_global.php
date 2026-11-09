<?php
require_once 'conexion.php';

$filtro = isset($_GET['query']) ? trim($_GET['query']) : '';

if ($filtro !== '') {
    // Buscamos en TODA la tabla de profesores por nombre o ID
    $sql = "SELECT * FROM profesores WHERE Nombre LIKE ? OR id_profesores LIKE ? ORDER BY id_profesores DESC LIMIT 50";
    $stmt = $conexion->prepare($sql);
    $term = "%" . $filtro . "%";
    $stmt->bind_param("ss", $term, $term);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    // Si está vacío, cargamos la lista completa por defecto
    $result = $conexion->query("SELECT * FROM profesores ORDER BY id_profesores DESC");
}

if ($result && $result->num_rows > 0) {
    $contador_visual = $result->num_rows;

    while ($fila = $result->fetch_assoc()) {
        $id_actual = (int) $fila['id_profesores'];
        echo "<tr>";
        echo "<td>" . $contador_visual . "</td>";
        echo "<td><b>" . htmlspecialchars($fila['Nombre']) . "</b></td>";
        // Ajustamos las celdas de acuerdo a tus columnas típicas de profesores
        echo "<td>" . htmlspecialchars($fila['Materia'] ?? '-') . "</td>";
        echo "<td>";
        echo '<a href="profesores.php?editar_id=' . $id_actual . '" style="text-decoration: none; padding: 3px 6px; background-color: #e2e3e5; border-radius: 3px; color: #383d41; font-size: 11px; margin-right: 5px;">Editar</a>';
        echo '<a href="profesores.php?eliminar_id=' . $id_actual . '" style="text-decoration: none; padding: 3px 6px; background-color: #f8d7da; border-radius: 3px; color: #721c24; font-size: 11px;" onclick="return confirm(\'¿Seguro(a) que deseas eliminar este profesor?\');">Eliminar</a>';
        echo "</td>";
        echo "</tr>";

        $contador_visual--;
    }
} else {
    echo '<tr><td colspan="4" style="text-align: center; color: #991B1B; font-weight:bold;">❌ No se encontraron profesores con ese nombre</td></tr>';
}
?>