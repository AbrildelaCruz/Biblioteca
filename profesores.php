<?php
include 'conexion.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Préstamos a profesores</title>
    <link rel="stylesheet" href="style.css?v=<?php echo time(); ?>">
</head>
<body>
    <div class="contenedor-formulario">
    <h2>Registrar Nuevo Profesor</h2>
    
    <form action="http://localhost:8080/PROYECTO%20DE%20TITULACION/Sistema_biblioteca/Biblioteca/profesores.php" method="POST">
        <div class="grupo-campo">
            <label for="num_empleado">Nombre</label>
            <input type="text" name="Nombre" maxlength="100" required>
        </div>

        <div class="grupo-campo">
            <label for="Materia">Materia</label>
            <input type="text" name="Materia" maxlength="100" required>
        </div>

        <div class="grupo-campo">
            <label for="Libro">Nombre del Libro</label>
            <input type="text" name="Libro" maxlength="100" required>
        </div>

        <div class="grupo-campo">
            <label for="Libro">Cantidad</label>
            <input type="text" name="Cantidad"  min="20" required>
        </div>

        <div class="grupo-campo">
            <label for="Fecha_prestamo">Fecha de préstamo</label>
            <input type="date" name="Fecha_prestamo" required>
        </div>

        <div class="grupo-campo">
            <label for="Fecha_prestamo">Fecha de entrega</label>
            <input type="date" name="Fecha_entrega" required>
        </div>

        <button type="submit" class="boton-guardar">Guardar Profesor</button>

    <table class="tabla-datos">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Materia</th>
                <th>Nombre del Libro</th>
                <th>Cantidad</th>
                <th>Fecha de préstamo</th>
                <th>Fecha de entrega</th>
            </tr>
        </thead>
        <tbody>
           
        </tbody>
    </table>
  <?php
//profesores
require_once __DIR__ . '/conexion.php';

$mensaje = '';
$tipoMensaje = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['Nombre'] ?? '');
    $materia = trim($_POST['Materia'] ?? '');
    $libro = trim($_POST['Libro'] ?? '');
    $cantidad = filter_input(INPUT_POST, 'Cantidad', FILTER_VALIDATE_INT);
    $fechaPrestamo = $_POST['Fecha_prestamo'] ?? '';
    $fechaEntrega = $_POST['Fecha_entrega'] ?? '';

    if ($nombre === '' || $libro === '' || $cantidad === false || $cantidad === null || $cantidad < 1 || $fechaPrestamo === '' || $fechaEntrega === '') {
        $mensaje = 'Completa todos los campos obligatorios. La cantidad debe ser mayor que cero.';
        $tipoMensaje = 'error';
    } elseif ($fechaEntrega < $fechaPrestamo) {
        $mensaje = 'La fecha de entrega no puede ser anterior a la fecha de préstamo.';
        $tipoMensaje = 'error';
    } else {
        
        $stmt = $conexion->prepare(
            'INSERT INTO profesores (Nombre, Materia, nombre_libro, Cantidad, Fecha_prestamo, Fecha_entrega) VALUES (?, ?, ?, ?, ?, ?)'
        );
        
        if (!$stmt) {
            $mensaje = 'No se pudo preparar el guardado: ' . $conexion->error;
            $tipoMensaje = 'error';
        } else {
            $stmt->bind_param('sssiss', $nombre, $materia, $libro, $cantidad, $fechaPrestamo, $fechaEntrega);
            if ($stmt->execute()) {
                $mensaje = 'Datos guardados en la tabla profesores.';
                $tipoMensaje = 'exito';
            } else {
                $mensaje = 'No se guardaron los datos en profesores: ' . $stmt->error;
                $tipoMensaje = 'error';
            }
            $stmt->close();
        }
    }
}
$resultado = $conexion->query(
    'SELECT id_Profesores, Nombre, Materia, Cantidad, Fecha_de_préstamo,Nombre_del_Libro, Fecha_de_entrega FROM profesores ORDER BY id_Profesores DESC'
);
?>

</body>

</html>