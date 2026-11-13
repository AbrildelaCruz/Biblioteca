<?php
require_once 'conexion.php';

$filtro = isset($_GET['query']) ? trim($_GET['query']) : '';

if ($filtro !== '') {
    $sql = "SELECT * FROM alumnos WHERE Nombre LIKE ? OR Matricula LIKE ? OR Grupo LIKE ? ORDER BY id_Alumnos DESC LIMIT 50";
    $stmt = $conexion->prepare($sql);
    $term = "%" . $filtro . "%";
    $stmt->bind_param("sss", $term, $term, $term);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $result = $conexion->query("SELECT * FROM alumnos ORDER BY id_Alumnos DESC");
}

if ($result && $result->num_rows > 0) {
    $contador_visual = $result->num_rows;
    while ($fila = $result->fetch_assoc()) {
        $id_actual = (int) $fila['id_Alumnos'];
        $mat = htmlspecialchars($fila['Matricula'] ?? 'Sin matrícula');
        echo "<tr>";
        echo "<td>" . $contador_visual . "</td>";
        echo "<td><span style='padding: 3px 8px; background: #D1FAE5; color: #065F46; border-radius: 12px; font-weight: bold; font-size: 11px;'>" . $mat . "</span></td>";
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
    echo '<tr><td colspan="8" style="text-align: center; color: #991B1B; font-weight:bold;">❌ No se encontraron alumnos con esa matrícula o nombre.</td></tr>';
}
?>