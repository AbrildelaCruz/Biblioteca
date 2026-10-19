<?php
require_once 'conexion.php';

$mensaje = '';
$tipoMensaje = '';


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $Nombre = trim($_POST['Nombre'] ?? '');
    $Materia = trim($_POST['Materia'] ?? '');
    $Cantidad = filter_input(INPUT_POST, 'Cantidad', FILTER_VALIDATE_INT);
    $Fecha_de_préstamo = $_POST['Fecha_prestamo'] ?? '';
    $Nombre_del_Libro = trim($_POST['Libro'] ?? '');
    $Fecha_de_entrega = $_POST['Fecha_entrega'] ?? '';

    if ($Nombre === '' || $Nombre_del_Libro === '' || $Cantidad === false || $Cantidad === null || $Cantidad < 1 || $Fecha_de_préstamo === '' || $Fecha_de_entrega === '') {
        $mensaje = 'Completa todos los campos obligatorios. La cantidad debe ser mayor que cero.';
        $tipoMensaje = 'error';
    } elseif ($Fecha_de_entrega < $Fecha_de_préstamo) {
        $mensaje = 'La fecha de entrega no puede ser anterior a la fecha de préstamo.';
        $tipoMensaje = 'error';
    } else {
        $stmt = $conexion->prepare(
            'INSERT INTO profesores (Nombre, Materia, Cantidad, Fecha_de_préstamo,Nombre_del_Libro, Fecha_de_entrega) VALUES (?, ?, ?, ?, ?, ?)'
        );
        
        if (!$stmt) {
            $mensaje = 'No se pudo preparar el guardado: ' . $conexion->error;
            $tipoMensaje = 'error';
        } else {
            $stmt->bind_param('sssiss', $Nombre, $Materia, $Cantidad, $Fecha_de_préstamo,$Nombre_del_Libro, $Fecha_de_entrega);
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

//Consulta a la tabla
$resultado = $conexion->query(
    'SELECT id_Profesores, Nombre, Materia, Cantidad, Fecha_de_préstamo, Nombre_del_Libro, Fecha_de_entrega FROM profesores ORDER BY id_Profesores DESC'
);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Préstamos a profesores</title>
    <!-- Aseguramos que use el fondo animado aplicando la clase body2 -->
    <link rel="stylesheet" href="style.css?v=<?php echo time(); ?>">
    <link rel="icon" type="image/png" href="logobiblioteca.png">
</head>
<body class="body2">

    <!-- Animación de burbujas de fondo -->
    <div class="contenedor-burbujas">
        <div class="burbuja"></div>
        <div class="burbuja"></div>
        <div class="burbuja"></div>
        <div class="burbuja"></div>
        <div class="burbuja"></div>
    </div>

    <?php if ($mensaje !== ''): ?>
        <div class="alerta <?php echo $tipoMensaje; ?>" style="padding: 10px; margin: 15px auto; max-width: 600px; text-align: center; border-radius: 5px; font-weight: bold; background-color: <?php echo $tipoMensaje === 'exito' ? '#d4edda' : '#f8d7da'; ?>; color: <?php echo $tipoMensaje === 'exito' ? '#155724' : '#721c24'; ?>;">
            <?php echo $mensaje; ?>
        </div>
    <?php endif; ?>

    <div class="contenedor-formulario">
        <h2>Registrar Nuevo Profesor</h2>
        
        <form action="profesores.php" method="POST">
            <div class="grupo-campo">
                <label>Nombre</label>
                <input type="text" name="Nombre" maxlength="100" required>
            </div>

            <div class="grupo-campo">
                <label>Materia</label>
                <input type="text" name="Materia" maxlength="100" required>
            </div>

            <div class="grupo-campo">
                <label>Cantidad</label>
                <input type="number" name="Cantidad" min="1" required>
            </div>

            <div class="grupo-campo">
                <label>Fecha de préstamo</label>
                <input type="date" name="Fecha_prestamo" required>
            </div>

            <div class="grupo-campo">
                <label>Nombre del Libro</label>
                <input type="text" name="Libro" maxlength="100" required>
            </div>

            <div class="grupo-campo">
                <label>Fecha de entrega</label>
                <input type="date" name="Fecha_entrega" required>
            </div>

            <button type="submit" class="boton-guardar">Guardar Profesor</button>
        </form> 
    </div> 

    <table class="tabla-datos">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Materia</th>
                <th>Cantidad</th>
                <th>Fecha de préstamo</th>
                <th>Nombre del Libro</th>
                <th>Fecha de entrega</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            // Conectar base de datos con la tabla
            if ($resultado && $resultado->num_rows > 0): 
                while ($fila = $resultado->fetch_assoc()): 
            ?>
                <tr>
                    <td><?php echo $fila['id_Profesores']; ?></td>
                    <td><?php echo htmlspecialchars($fila['Nombre']); ?></td>
                    <td><?php echo htmlspecialchars($fila['Materia']); ?></td>
                    <td><?php echo htmlspecialchars($fila['Nombre_del_Libro']); ?></td>
                    <td><?php echo $fila['Cantidad']; ?></td>
                    <td><?php echo $fila['Fecha_de_prestamo']; ?></td>
                    <td><?php echo $fila['Fecha_de_entrega']; ?></td>
                    <td><button class="btn-eliminar" onclick="eliminarFila(this)">Eliminar</button></td>
                </tr>
            <?php 
                endwhile; 
            else: 
            ?>
                <tr>
                    <td colspan="7" style="text-align: center;">No hay préstamos registrados actualmente.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <div class="z1">
        <a class="z2" href="registro.html">REGRESAR</a>
    </div>

</body>
</html>