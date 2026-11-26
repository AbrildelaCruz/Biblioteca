<?php
session_start();
if (!isset($_SESSION['admin_autenticado']) || $_SESSION['admin_autenticado'] !== true) {
    header("Location: login.php");
    exit();
}
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
    $sql = "SELECT 
                id_Alumnos AS id, 
                IFNULL(Matricula, 'S/M') AS matricula, 
                TRIM(CONCAT_WS(' ', Nombre, Apellido_Paterno, Apellido_Materno)) AS nombre,
                IFNULL(NULLIF(Madre_encargada, ''), '-') AS madre,
                IFNULL(NULLIF(Familia, ''), '-') AS familia,
                IFNULL(NULLIF(Piso, ''), '-') AS piso,
                IFNULL(NULLIF(Grupo, ''), '-') AS grupo
            FROM alumnos 
            WHERE Matricula LIKE ? 
               OR CONCAT_WS(' ', Nombre, Apellido_Paterno, Apellido_Materno) LIKE ? 
            ORDER BY Nombre ASC 
            LIMIT 8";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("ss", $busqueda, $busqueda);
    $stmt->execute();
    $res = $stmt->get_result();
    while ($fila = $res->fetch_assoc()) {
        $resultados[] = $fila;
    }
    $stmt->close();
} elseif ($tipo === 'profesor') {
    $sql = "SELECT 
                id_profesores AS id, 
                IFNULL(Matricula, 'S/M') AS matricula, 
                TRIM(CONCAT_WS(' ', Nombre, Apellido_Paterno, Apellido_Materno)) AS nombre,
                IFNULL(NULLIF(Materia, ''), '-') AS materia
            FROM profesores 
            WHERE Matricula LIKE ? 
               OR CONCAT_WS(' ', Nombre, Apellido_Paterno, Apellido_Materno) LIKE ? 
            ORDER BY Nombre ASC 
            LIMIT 8";
    $stmt = $conexion->prepare($sql);
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