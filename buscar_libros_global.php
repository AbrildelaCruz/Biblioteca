<?php
require_once 'conexion.php';

$query = isset($_GET['query']) ? trim($_GET['query']) : '';
$categoria = isset($_GET['categoria']) ? trim($_GET['categoria']) : '';

$condiciones = [];
$params = [];
$tipos = "";

if ($query !== '') {
    $condiciones[] = "(Titulo LIKE ? OR Autor LIKE ? OR Editorial LIKE ? OR ISBN LIKE ? OR `Clasificación` LIKE ?)";
    $busqueda = "%" . $query . "%";
    array_push($params, $busqueda, $busqueda, $busqueda, $busqueda, $busqueda);
    $tipos .= "sssss";
}

if ($categoria !== '') {
    $condiciones[] = "Categoria = ?";
    $params[] = $categoria;
    $tipos .= "s";
}

if (!empty($condiciones)) {
    $sql = "SELECT * FROM libros WHERE " . implode(" AND ", $condiciones) . " ORDER BY id_libro ASC";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param($tipos, ...$params);
    $stmt->execute();
    $resultado = $stmt->get_result();
} else {
    $resultado = $conexion->query("SELECT * FROM libros ORDER BY id_libro ASC LIMIT 15 OFFSET 0");
}

if ($resultado && $resultado->num_rows > 0) {
    $num = 1;
    while ($fila = $resultado->fetch_assoc()) {
        $id_libro = (int) $fila['id_libro'];
        $clasif = $fila['Clasificación'] ?? $fila['clasificacion'] ?? '-';
        $totales = $fila['Ejemplares_totales'] ?? 0;
        $disp = $fila['Ejemplares_disponibles'] ?? 0;
        $prest = $fila['Ejemplares_prestados'] ?? 0;

        echo "<tr>";
        echo "<td style='font-weight: bold; text-align: center;'>" . $num . "</td>";
        echo "<td><b>" . htmlspecialchars($fila['Titulo']) . "</b></td>";
        echo "<td>" . htmlspecialchars($fila['Autor']) . "</td>";
        echo "<td><span style='padding: 3px 7px; background: #EFF6FF; color: #1E40AF; border-radius: 12px; font-size: 11px; font-weight: bold;'>" . htmlspecialchars($fila['Categoria'] ?? 'Novela y Narrativa') . "</span></td>";
        echo "<td>" . htmlspecialchars($fila['Editorial']) . "</td>";
        echo "<td>" . htmlspecialchars($fila['ISBN'] ?? '-') . "</td>";
        echo "<td>" . htmlspecialchars($fila['ano'] ?? '-') . "</td>";
        echo "<td>" . htmlspecialchars($clasif) . "</td>";
        echo "<td style='text-align: center;'>" . $totales . "</td>";
        echo "<td style='text-align: center; color: green; font-weight: bold;'>" . $disp . "</td>";
        echo "<td style='text-align: center; color: red;'>" . $prest . "</td>";
        echo "<td>";
        echo '<a href="catalogo.php?editar_id=' . $id_libro . '" style="text-decoration: none; padding: 3px 6px; background-color: #e2e3e5; border-radius: 3px; color: #383d41; font-size: 11px;">Editar</a>';
        echo '<a href="catalogo.php?eliminar_id=' . $id_libro . '" style="text-decoration: none; padding: 3px 6px; background-color: #f8d7da; border-radius: 3px; color: #721c24; font-size: 11px; margin-left: 2px;" onclick="return confirm(\'¿Seguro que deseas eliminar este libro?\');">Eliminar</a>';
        echo "</td>";
        echo "</tr>";
        $num++;
    }
} else {
    echo '<tr><td colspan="12" style="text-align: center; color: #991B1B; font-weight: bold; padding: 15px;">❌ No se encontraron libros con esos filtros.</td></tr>';
}
?>