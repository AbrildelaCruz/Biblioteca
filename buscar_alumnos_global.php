<?php
require_once 'conexion.php';

$filtro = isset($_GET['query']) ? trim($_GET['query']) : '';

if ($filtro !== '') {
    // Buscamos en TODA la base de datos por nombre o ID
    $sql = "SELECT * FROM alumnos WHERE Nombre LIKE ? OR id_Alumnos LIKE ? ORDER BY id_Alumnos DESC LIMIT 50";
    $stmt = $conexion->prepare($sql);
    $term = "%" . $filtro . "%";
    $stmt->bind_param("ss", $term, $term);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    // Si está vacío, volvemos a cargar tu consulta original por defecto
    $result = $conexion->query("SELECT * FROM alumnos ORDER BY id_Alumnos DESC");
}

if ($result && $result->num_rows > 0) {
    $contador_visual = $result->num_rows;

    while ($fila = $result->fetch_assoc()) {
        $id_actual = (int) $fila['id_Alumnos'];
        echo "<tr>";
        echo "<td>" . $contador_visual . "</td>";
        echo "<td><b>" . htmlspecialchars($fila['Nombre']) . "</b></td>";
        echo "<td>" . htmlspecialchars($fila['Madre_encargada'] ?? '-') . "</td>";
        echo "<td>" . htmlspecialchars($fila['Familia'] ?? '-') . "</td>";
        echo "<td style='text-align: center;'>" . htmlspecialchars($fila['Piso'] ?? '-') . "</td>";
        echo "<td style='text-align: center;'>" . htmlspecialchars($fila['Grupo'] ?? '-') . "</td>";
        echo "<td>";
        echo '<a href="alumnos.php?editar_id=' . $id_actual . '" style="text-decoration: none; padding: 3px 6px; background-color: #e2e3e5; border-radius: 3px; color: #383d41; font-size: 11px; margin-right: 5px;">Editar</a>';
        echo '<a href="alumnos.php?eliminar_id=' . $id_actual . '" style="text-decoration: none; padding: 3px 6px; background-color: #f8d7da; border-radius: 3px; color: #721c24; font-size: 11px;" onclick="return confirm(\'¿Seguro que deseas eliminar este alumno?\');">Eliminar</a>';
        echo "</td>";
        echo "</tr>";

        $contador_visual--;
    }
} else {
    echo '<tr><td colspan="7" style="text-align: center; color: #991B1B; font-weight:bold;">❌ No se encontraron alumnos con ese nombre en toda la base de datos.</td></tr>';
}
?>