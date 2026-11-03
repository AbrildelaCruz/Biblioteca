<?php
require_once 'conexion.php';

header("Content-Type: application/json");

// Recibir la solicitud JSON desde JavaScript
$input = json_decode(file_get_contents('php://input'), true);

if (!$input) {
    echo json_encode(['success' => false, 'message' => 'Faltan parámetros de entrada.']);
    exit;
}

$rol = trim($input['rol'] ?? '');
$nombre = trim($input['nombre'] ?? '');

if ($rol === '' || $nombre === '') {
    echo json_encode(['success' => false, 'message' => 'Escribe un criterio de búsqueda.']);
    exit;
}

$termino = "%" . $nombre . "%";

if ($rol === 'alumno') {
    // Consulta exacta usando tus columnas reales de la tabla alumnos
    $stmt = $conexion->prepare("SELECT id_Alumnos, Nombre, Madre_encargada, Familia, Piso, Grupo FROM alumnos WHERE Nombre LIKE ? LIMIT 1");
    if ($stmt) {
        $stmt->bind_param("s", $termino);
        $stmt->execute();
        $resultado = $stmt->get_result();
        $alumno = $resultado->fetch_assoc();
        
        if ($alumno) {
            echo json_encode(['success' => true, 'data' => $alumno]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Alumno no localizado.']);
        }
        $stmt->close();
    }
} elseif ($rol === 'profesor') {
    // Consulta exacta usando tus columnas reales de la tabla profesores
    $stmt = $conexion->prepare("SELECT id_profesores, Nombre, Materia FROM profesores WHERE Nombre LIKE ? LIMIT 1");
    if ($stmt) {
        $stmt->bind_param("s", $termino);
        $stmt->execute();
        $resultado = $stmt->get_result();
        $profesor = $resultado->fetch_assoc();
        
        if ($profesor) {
            echo json_encode(['success' => true, 'data' => $profesor]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Profesor no localizado.']);
        }
        $stmt->close();
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Rol inválido.']);
}
exit();
?>