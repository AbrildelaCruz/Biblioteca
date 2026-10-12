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
    
    <form action="http://localhost:8080/PROYECTO%20DE%20TITULACION/Sistema_biblioteca/Biblioteca/controladores/guardar_profesor.php" method="POST">
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
    </form>
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