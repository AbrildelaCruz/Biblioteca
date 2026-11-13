<?php
require_once 'conexion.php';
header('Content-Type: application/json; charset=utf-8');

$tipo = trim($_GET['tipo'] ?? '');
$q = trim($_GET['q'] ?? '');

if ($tipo === '' || $q === '') {
    echo json_encode([]);
    exit();
}

$busqueda = "%" . $q . "%";
$resultados = [];

if ($tipo === 'alumno') {
    $stmt = $conexion->prepare("SELECT id_Alumnos AS id, IFNULL(Matricula, 'Pendiente') AS matricula, Nombre AS nombre, IFNULL(Madre_encargada, '-') AS madre, IFNULL(Familia, '-') AS familia, IFNULL(Piso, '-') AS piso, IFNULL(Grupo, '-') AS grupo FROM alumnos WHERE Matricula LIKE ? OR Nombre LIKE ? ORDER BY Nombre ASC LIMIT 10");
    $stmt->bind_param("ss", $busqueda, $busqueda);
    $stmt->execute();
    $res = $stmt->get_result();
    while ($fila = $res->fetch_assoc()) {
        $resultados[] = $fila;
    }
    $stmt->close();
} elseif ($tipo === 'profesor') {
    $stmt = $conexion->prepare("SELECT id_profesores AS id, Matricula AS matricula, Nombre AS nombre, IFNULL(Materia, '-') AS materia FROM profesores WHERE Matricula LIKE ? OR Nombre LIKE ? ORDER BY Nombre ASC LIMIT 10");
    $stmt->bind_param("ss", $busqueda, $busqueda);
    $stmt->execute();
    $res = $stmt->get_result();
    while ($fila = $res->fetch_assoc()) {
        $resultados[] = $fila;
    }
    $stmt->close();
}

echo json_encode($resultados);
?>