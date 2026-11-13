<?php
require_once 'conexion.php';
header("Content-Type: application/json");
$input = json_decode(file_get_contents('php://input'), true);
$rol = trim($input['rol'] ?? '');
$nombre = trim($input['nombre'] ?? '');

if ($rol === '' || $nombre === '') {
    echo json_encode(['success' => false, 'message' => 'Escribe matrícula o nombre.']);
    exit;
}
$termino = "%" . $nombre . "%";

if ($rol === 'alumno') {
    $stmt = $conexion->prepare("SELECT id_Alumnos, Matricula, Nombre, Madre_encargada, Familia, Piso, Grupo FROM alumnos WHERE Matricula = ? OR Nombre LIKE ? LIMIT 1");
    $stmt->bind_param("ss", $nombre, $termino);
    $stmt->execute();
    $alumno = $stmt->get_result()->fetch_assoc();
    echo $alumno ? json_encode(['success' => true, 'data' => $alumno]) : json_encode(['success' => false, 'message' => 'Alumno no localizado.']);
} elseif ($rol === 'profesor') {
    $stmt = $conexion->prepare("SELECT id_profesores, Nombre, Materia FROM profesores WHERE Nombre LIKE ? LIMIT 1");
    $stmt->bind_param("s", $termino);
    $stmt->execute();
    $profesor = $stmt->get_result()->fetch_assoc();
    echo $profesor ? json_encode(['success' => true, 'data' => $profesor]) : json_encode(['success' => false, 'message' => 'Profesor no localizado.']);
}
?>