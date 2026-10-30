<?php
require_once 'conexion.php';

header("Content-Type: application/json");
date_default_timezone_set('America/Mexico_City');
$fecha_hoy = date('Y-m-d');

// Consulta exacta uniendo tus tablas de préstamos, libros, alumnos y profesores
$sql = "SELECT 
            l.Titulo AS Libro,
            p.fecha_devolucion_esperada AS FechaLimite,
            COALESCE(a.Nombre, prof.Nombre, 'Desconocido') AS Persona,
            DATEDIFF(p.fecha_devolucion_esperada, '$fecha_hoy') AS DiasRestantes
        FROM prestamos p
        JOIN libros l ON p.id_libro = l.id_libro
        LEFT JOIN alumnos a ON p.id_alumnos = a.id_Alumnos
        LEFT JOIN profesores prof ON p.id_profesores = prof.id_profesores
        WHERE p.fecha_devolucion_real IS NULL 
          AND p.tipo_movimiento = 'Prestado'
        ORDER BY p.fecha_devolucion_esperada ASC";

$resultado = $conexion->query($sql);
$alertas = [];

if ($resultado && $resultado->num_rows > 0) {
    while ($fila = $resultado->fetch_assoc()) {
        $alertas[] = [
            "libro" => $fila['Libro'],
            "persona" => $fila['Persona'],
            "fecha_limite" => date('d/m/Y', strtotime($fila['FechaLimite'])),
            "dias" => (int) $fila['DiasRestantes']
        ];
    }
}

echo json_encode($alertas);
exit();
?>