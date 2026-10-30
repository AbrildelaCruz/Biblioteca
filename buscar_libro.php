<?php
require_once 'conexion.php';

// 2. Recibir los datos en formato JSON enviados por JavaScript
$input = json_decode(file_get_contents('php://input'), true);

if (!$input) {
    echo json_encode(['success' => false, 'message' => 'Sin datos de entrada']);
    exit;
}

$condiciones = [];
$valores = [];
$tipos = ""; // Guardará los tipos de datos para el bind_param (ej: "sss")

// 3. Analizar qué campos envió el usuario rellenos
foreach ($input as $columna => $valor) {
    if (!empty(trim($valor))) {
        // Añadimos la condición SQL buscando coincidencias parciales (LIKE)
        $condiciones[] = "`$columna` LIKE ?";
        $valores[] = "%" . trim($valor) . "%";
        $tipos .= "s"; // 's' significa que es un string (cadena de texto)
    }
}

if (empty($condiciones)) {
    echo json_encode(['success' => false, 'message' => 'Falta llenar campos']);
    exit;
}

// 4. Unimos todas las condiciones con un OR para buscar por cualquiera de los campos rellenos
$sql = "SELECT * FROM libros WHERE " . implode(' OR ', $condiciones) . " LIMIT 1";

// 5. Preparamos y ejecutamos la consulta de forma segura con MySQLi
$stmt = $conexion->prepare($sql);

if ($stmt) {
    // Vinculamos los parámetros dinámicamente
    $stmt->bind_param($tipos, ...$valores);
    $stmt->execute();

    // Obtenemos el resultado
    $resultado = $stmt->get_result();
    $libro = $resultado->fetch_assoc();

    if ($libro) {
        echo json_encode(['success' => true, 'libro' => $libro]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Libro no encontrado']);
    }

    $stmt->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Error en la preparación de la consulta']);
}
?>