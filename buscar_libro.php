<?php
require_once 'conexion.php';

if (isset($_GET['id'])) {
    $id_libro = mysqli_real_escape_string($conexion, $_GET['id']);
    
    // Ejecuta la consulta usando tu llave primaria exacta
    $query = "SELECT * FROM libros WHERE id_libro = '$id_libro'";
    $resultado = $conexion->query($query);
    
    if ($resultado && $fila = $resultado->fetch_assoc()) {
        // Regresa la fila completa mapeada en un JSON hacia la funcion JavaScript
        echo json_encode($fila);
    } else {
        echo json_encode(['error' => 'Libro no encontrado']);
    }
}
?>