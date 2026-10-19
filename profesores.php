<?php
require_once 'conexion.php';

$mensaje = '';
$tipoMensaje = '';

// 1. RECUPERAR MENSAJES DE LA SESIÓN SI EXISTEN
$mensaje = $_SESSION['mensaje'] ?? '';
$tipoMensaje = $_SESSION['tipoMensaje'] ?? '';

unset($_SESSION['mensaje'], $_SESSION['tipoMensaje']);

// 2. PROCESAR EL FORMULARIO POR MÉTODO POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $Nombre = trim($_POST['Nombre'] ?? '');
    $Materia = trim($_POST['Materia'] ?? '');
    $Cantidad = filter_input(INPUT_POST, 'Cantidad', FILTER_VALIDATE_INT);
    $Fecha_de_préstamo = $_POST['Fecha_prestamo'] ?? '';
    $Nombre_del_Libro = trim($_POST['Libro'] ?? '');
    $Fecha_de_entrega = $_POST['Fecha_entrega'] ?? '';

    if ($Nombre === '' || $Nombre_del_Libro === '' || $Cantidad === false || $Cantidad === null || $Cantidad < 1 || $Fecha_de_préstamo === '' || $Fecha_de_entrega === '') {
        $_SESSION['mensaje'] = 'Completa todos los campos obligatorios. La cantidad debe ser mayor que cero.';
        $_SESSION['tipoMensaje'] = 'error';
    } elseif ($Fecha_de_entrega < $Fecha_de_préstamo) {
        $_SESSION['mensaje'] = 'La fecha de entrega no puede ser anterior a la fecha de préstamo.';
        $_SESSION['tipoMensaje'] = 'error';
    } else {
        $stmt = $conexion->prepare(
            'INSERT INTO profesores (Nombre, Materia, Cantidad, Fecha_de_préstamo, Nombre_del_Libro, Fecha_de_entrega) VALUES (?, ?, ?, ?, ?, ?)'
        );
        
        if (!$stmt) {
            $_SESSION['mensaje'] = 'No se pudo preparar el guardado: ' . $conexion->error;
            $_SESSION['tipoMensaje'] = 'error';
        } else {
            $stmt->bind_param('sssiss', $Nombre, $Materia, $Cantidad, $Fecha_de_préstamo, $Nombre_del_Libro, $Fecha_de_entrega);
            if ($stmt->execute()) {
                $_SESSION['mensaje'] = 'Datos guardados en la tabla profesores.';
                $_SESSION['tipoMensaje'] = 'exito';
            } else {
                $_SESSION['mensaje'] = 'No se guardaron los datos en profesores: ' . $stmt->error;
                $_SESSION['tipoMensaje'] = 'error';
            }
            $stmt->close();
        }
    }
    header("Location: profesores.php");
    exit();
}

// 3. CONSULTA REAL A LA TABLA
$resultado = $conexion->query(
    'SELECT id_Profesores, Nombre, Materia, Cantidad, Fecha_de_préstamo, Nombre_del_Libro, Fecha_de_entrega FROM profesores ORDER BY id_Profesores DESC'
);

if (isset($_GET['eliminar_id'])) {
    $id_a_borrar = filter_input(INPUT_GET, 'eliminar_id', FILTER_VALIDATE_INT);
    if ($id_a_borrar) {
        $stmt_borrar = $conexion->prepare('DELETE FROM profesores WHERE id_Profesores = ?');
        if ($stmt_borrar) {
            $stmt_borrar->bind_param('i', $id_a_borrar);
            if ($stmt_borrar->execute()) {
                $_SESSION['mensaje'] = 'Registro eliminado correctamente.';
                $_SESSION['tipoMensaje'] = 'exito';
            } else {
                $_SESSION['mensaje'] = 'Error al eliminar en la base de datos.';
                $_SESSION['tipoMensaje'] = 'error';
            }
            $stmt_borrar->close();
        }
    }
    header("Location: profesores.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Préstamos a profesores</title>
    <link rel="stylesheet" href="style.css?v=<?php echo time(); ?>">
    <link rel="icon" type="image/png" href="logobiblioteca.png">
</head>
<body class="body2">

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
                <th>-</th>
            </tr>
        </thead>
        <tbody>
            <?php 
                if ($resultado && $resultado->num_rows > 0):
                while ($fila = $resultado->fetch_assoc()):
                ?>
                <tr>
                <td><?php echo $fila['id_Profesores']; ?></td>
                <td><?php echo htmlspecialchars($fila['Nombre']); ?></td>
                <td><?php echo htmlspecialchars($fila['Materia']); ?></td>
                <td><?php echo $fila['Cantidad']; ?></td>
                <td><?php echo $fila['Fecha_de_préstamo']; ?></td>
                <td><?php echo htmlspecialchars($fila['Nombre_del_Libro']); ?></td>
                <td><?php echo $fila['Fecha_de_entrega']; ?></td>
                <td><a href="profesores.php?eliminar_id=<?php echo $fila['id_Profesores']; ?>" class="btn-eliminar" style="text-decoration: none; display: inline-block; padding: 5px 10px; background-color: #fff; border: 1px solid #ccc; border-radius: 3px; color: black; font-size: 13px;" onclick="return confirm('¿Seguro que deseas eliminar este registro permanente?');">Eliminar</a><td>
    
</td></td>
                </tr>
                    </tr>
            <?php 
                endwhile; 
            else: 
            ?>
                <tr>
                    <td colspan="8" style="text-align: center;">No hay préstamos registrados actualmente.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <script>

    function eliminarFila(boton) {

        
    function eliminarFila(boton) {
      const fila = boton.parentNode.parentNode;
      fila.remove();
    }
    </script>

    <div class="z1">
        <a class="z2" href="registro.html">REGRESAR</a>
    </div>

</body>
</html>