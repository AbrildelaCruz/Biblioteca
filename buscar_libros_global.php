<?php
session_start();
if (!isset($_SESSION['admin_autenticado']) || $_SESSION['admin_autenticado'] !== true) {
    header("Location: login.php");
    exit();
}
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
        $id_actual = $fila['acciones']?? 0;

        echo "<tr>";
        echo "<td style='font-weight: bold; text-align: center;'>" . $num . "</td>";
        echo "<td><strong>" . htmlspecialchars($fila['Titulo']) . "</strong></td>";
        echo "<td>" . htmlspecialchars($fila['Autor']) . "</td>";
        echo "<td><span class='badge-categoria'>" . htmlspecialchars($fila['Categoria'] ?? 'Novela y Narrativa') . "</span></td>";
        echo "<td>" . htmlspecialchars($fila['Editorial']) . "</td>";
        echo "<td style='white-space: nowrap; font-family: monospace; font-size: 13px;'>" . htmlspecialchars($fila['ISBN'] ?? '-') . "</td>";
        echo "<td style='text-align: center;'>" . htmlspecialchars($fila['ano'] ?? '-') . "</td>";
        echo "<td style='white-space: nowrap;'>" . htmlspecialchars($clasif) . "</td>";
        echo "<td style='text-align: center; font-weight: 600;'>" . $totales . "</td>";
        echo "<td style='text-align: center; color: #16A34A; font-weight: bold;'>" . $disp . "</td>";
        echo "<td style='text-align: center; color: #DC3545; font-weight: 600;'>" . $prest . "</td>";
        echo "<td style='text-align: center;'>";
        echo '<a href="catalogo.php?editar_id=' . $id_libro . '" class="btn-editar">✏️ Editar</a>';
        echo '<a href="catalogo.php?eliminar_id=' . $id_libro . '" class="btn-eliminar" onclick="return confirm(\'¿Seguro que deseas eliminar este libro?\');">🗑️ Eliminar</a>';
        echo "</td>";
        echo "</tr>";
        $num++;
    }

} else {
    echo '<tr><td colspan="12" style="text-align: center; color: #991B1B; font-weight: bold; padding: 15px;">❌ No se encontraron libros con esos filtros.</td></tr>';
}
?>