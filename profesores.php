<?php
include 'conexion.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Préstamos a profesores</title>
        <style>
            .formulario, .tabla-datos { width: 95%; max-width: 1000px; margin: 20px auto; }
            .formulario { display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 12px; }
            .formulario label { display: flex; flex-direction: column; gap: 5px; }
            .formulario button { padding: 10px; cursor: pointer; }
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
    <h1>Préstamos a profesores</h1>
    <div class="p1"><a class="p2" href="registro.html">REGRESAR</a></div>

    <?php if ($mensaje !== ''): ?>
        <p class="mensaje <?= htmlspecialchars($tipoMensaje, ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($mensaje, ENT_QUOTES, 'UTF-8') ?></p>
    <?php endif; ?>

    <form class="formulario" method="post" action="profesores.php">
        <label>Nombre del profesor *<input type="text" name="Nombre" maxlength="100" required></label>
        <label>Materia<input type="text" name="Materia" maxlength="100"></label>
        <label>Nombre del Libro *<input type="text" name="Libro" maxlength="100" required></label>
        <label>Cantidad *<input type="number" name="Cantidad" min="1" required></label>
        <label>Fecha de préstmo *<input type="date" name="Fecha_prestamo" required></label>
        <label>Fecha de entrega *<input type="date" name="Fecha_entrega" required></label>
        <button type="submit">Guardar registro</button>
    </form>

    <table class="tabla-datos">
        <thead>
            <tr>
                <th>id_Profesores</th><th>Nombre</th><th>Materia</th><th>Libro</th>
                <th>Cantidad</th><th>Fecha de préstamo</th><th>Fecha de entrega</th>
            </tr>
        </thead>
        <tbody>
            
        </tbody>
    </table>
    <?php $conexion->close(); ?>
</body>
</html>