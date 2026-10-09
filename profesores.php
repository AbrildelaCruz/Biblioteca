<?php
require_once __DIR__ . '/conexion.php';

$mensaje = '';
$tipoMensaje = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['Nombre'] ?? '');
    $materia = trim($_POST['Materia'] ?? '');
    $libro = trim($_POST['Nombre del Libro'] ?? '');
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
            'INSERT INTO profesores (Nombre, Materia, Nombre del Libro, Cantidad, Fecha_prestamo, Fecha_entrega) VALUES (?, ?, ?, ?, ?, ?)'
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
    'SELECT id_Profesores, Nombre, Materia, Nombre del Libro, Cantidad, Fecha_prestamo, Fecha_entrega FROM profesores ORDER BY id_Profesores DESC'
);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Préstamos a profesores</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 24px; }
        .formulario, .tabla-datos { width: 95%; max-width: 1000px; margin: 20px auto; }
        .formulario { display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 12px; }
        .formulario label { display: flex; flex-direction: column; gap: 5px; }
        .formulario input, .formulario button { padding: 9px; }
        .formulario button { cursor: pointer; }
        .tabla-datos { border-collapse: collapse; }
        .tabla-datos th, .tabla-datos td { border: 1px solid #ddd; padding: 10px; text-align: left; }
        .tabla-datos th { background: #2c3e50; color: white; }
        .tabla-datos tr:nth-child(even) { background: #f5f5f5; }
        .mensaje { max-width: 1000px; margin: 16px auto; }
        .error { color: #b00020; }
        .exito { color: #16753b; }
    </style>
</head>
<body>
    <h1 style="text-align:center">Registro de profesores</h1>
    <p style="text-align:center"><a href="registro.html">Regresar</a></p>

    <?php if ($mensaje !== ''): ?>
        <p class="mensaje <?= htmlspecialchars($tipoMensaje, ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($mensaje, ENT_QUOTES, 'UTF-8') ?></p>
    <?php endif; ?>

    <form class="formulario" method="post" action="profesores.php">
        <label>Nombre del profesor *<input type="text" name="Nombre" maxlength="100" required></label>
        <label>Materia<input type="text" name="Materia" maxlength="100"></label>
        <label>Nombre del libro *<input type="text" name="Libro" maxlength="100" required></label>
        <label>Cantidad *<input type="number" name="Cantidad" min="1" required></label>
        <label>Fecha de préstamo *<input type="date" name="Fecha_prestamo" required></label>
        <label>Fecha de entrega *<input type="date" name="Fecha_entrega" required></label>
        <button type="submit">Guardar profesor</button>
    </form>

    <table class="tabla-datos">
        <thead>
            <tr>
                <th>ID</th><th>Nombre</th><th>Materia</th><th>Libro</th>
                <th>Cantidad</th><th>Fecha de préstamo</th><th>Fecha de entrega</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($resultado && $resultado->num_rows > 0): ?>
                <?php while ($fila = $resultado->fetch_assoc()): ?>
                    <tr>
                        <td><?= (int) $fila['id_Profesores'] ?></td>
                        <td><?= htmlspecialchars($fila['Nombre'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars($fila['Materia'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars($fila['Nombre del Libro'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= (int) $fila['Cantidad'] ?></td>
                        <td><?= htmlspecialchars($fila['Fecha_prestamo'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars($fila['Fecha_entrega'], ENT_QUOTES, 'UTF-8') ?></td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="7" style="text-align:center">No hay registros de profesores.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
    <?php $conexion->close(); ?>
</body>
</html>