<?php
require_once 'conexion.php';
header('Content-Type: application/json; charset=utf-8');

$q = trim($_GET['q'] ?? '');

if ($q === '') {
    echo json_encode([]);
    exit();
}

$busqueda = "%" . $q . "%";
$resultados = [];

$sql = "SELECT id_libro AS id, 
               Titulo AS titulo, 
               Autor AS autor, 
               IFNULL(Editorial, '-') AS editorial, 
               IFNULL(Categoria, 'Novela y Narrativa') AS categoria, 
               IFNULL(ISBN, 'Sin ISBN') AS isbn, 
               Ejemplares_disponibles AS disponibles 
        FROM libros 
        WHERE Ejemplares_disponibles > 0 
          AND (Titulo LIKE ? OR Autor LIKE ? OR ISBN LIKE ?) 
        ORDER BY Titulo ASC 
        LIMIT 10";

$stmt = $conexion->prepare($sql);
$stmt->bind_param("sss", $busqueda, $busqueda, $busqueda);
$stmt->execute();
$res = $stmt->get_result();

while ($fila = $res->fetch_assoc()) {
    $resultados[] = $fila;
}
$stmt->close();

echo json_encode($resultados);
?>