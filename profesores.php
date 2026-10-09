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
  
</body>
</html>