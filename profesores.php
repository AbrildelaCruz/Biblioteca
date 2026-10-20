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
    $id_libro = filter_input(INPUT_POST, 'id_libro', FILTER_VALIDATE_INT);
    $Fecha_de_entrega = $_POST['Fecha_entrega'] ?? '';

    if ($Nombre === '' || !$id_libro || $Cantidad === false || $Cantidad < 1 || $Fecha_de_préstamo === '' || $Fecha_de_entrega === '') {
        $_SESSION['mensaje'] = 'Completa todos los campos obligatorios. La cantidad debe ser mayor que cero.';
        $_SESSION['tipoMensaje'] = 'error';
    } elseif ($Fecha_de_entrega < $Fecha_de_préstamo) {
        $_SESSION['mensaje'] = 'La fecha de entrega no puede ser anterior a la fecha de préstamo.';
        $_SESSION['tipoMensaje'] = 'error';
    } else {
        // Desactivar temporalmente autocommit para trabajar con transacciones de forma segura
        $conexion->begin_transaction();

        try {
            // Verificar si el profesor ya existe para no duplicarlo en la tabla profesores
            $stmt_prof = $conexion->prepare("SELECT id_Profesores FROM profesores WHERE Nombre = ? AND Materia = ?");
            $stmt_prof->bind_param("ss", $Nombre, $Materia);
            $stmt_prof->execute();
            $res_prof = $stmt_prof->get_result();
            
            if ($res_prof->num_rows > 0) {
                $profesor = $res_prof->fetch_assoc();
                $id_profesor_actual = $profesor['id_Profesores'];
            } else {
                // Si no existe, se registra como nuevo profesor fijo
                $stmt_ins_prof = $conexion->prepare("INSERT INTO profesores (Nombre, Materia) VALUES (?, ?)");
                $stmt_ins_prof->bind_param("ss", $Nombre, $Materia);
                $stmt_ins_prof->execute();
                $id_profesor_actual = $conexion->insert_id;
                $stmt_ins_prof->close();
            }
            $stmt_prof->close();

            // Insertar el préstamo real en la tabla relacional de 'prestamos'
            $stmt_prestamo = $conexion->prepare(
                'INSERT INTO prestamos (id_libros, id_profesores, Cantidad, fecha_salida, fecha_devolucion_esperada) VALUES (?, ?, ?, ?, ?)'
            );
            $stmt_prestamo->bind_param('iiiss', $id_libro, $id_profesor_actual, $Cantidad, $Fecha_de_préstamo, $Fecha_de_entrega);
            $stmt_prestamo->execute();
            $stmt_prestamo->close();

            // Confirmar inserciones en la Base de Datos
            $conexion->commit();
            
            $_SESSION['mensaje'] = 'Préstamo registrado correctamente en la base de datos relacional.';
            $_SESSION['tipoMensaje'] = 'exito';

        } catch (Exception $e) {
            // Si algo falla, revertimos los cambios para no dejar datos corruptos
            $conexion->rollback();
            $_SESSION['mensaje'] = 'Error al procesar el préstamo: ' . $e->getMessage();
            $_SESSION['tipoMensaje'] = 'error';
        }
    }
    header("Location: profesores.php");
    exit();
}

// 3. PROCESAR ACCIÓN DE ELIMINAR REGISTRO DE PRÉSTAMO
if (isset($_GET['eliminar_id'])) {
    $id_a_borrar = filter_input(INPUT_GET, 'eliminar_id', FILTER_VALIDATE_INT);
    if ($id_a_borrar) {
        $stmt_borrar = $conexion->prepare('DELETE FROM prestamos WHERE id_prestamo = ?');
        if ($stmt_borrar) {
            $stmt_borrar->bind_param('i', $id_a_borrar);
            if ($stmt_borrar->execute()) {
                $_SESSION['mensaje'] = 'Préstamo eliminado correctamente.';
                $_SESSION['tipoMensaje'] = 'exito';
            } else {
                $_SESSION['mensaje'] = 'Error al eliminar el préstamo en la base de datos.';
                $_SESSION['tipoMensaje'] = 'error';
            }
            $stmt_borrar->close();
        }
    }
    header("Location: profesores.php");
    exit();
}

// 4. CONSULTA RELACIONAL USANDO INNER JOIN PARA ARMAR LA TABLA VISUAL
$resultado = $conexion->query(
    'SELECT pr.id_prestamo, p.Nombre, p.Materia, pr.Cantidad, pr.fecha_salida, l.Titulo AS Nombre_del_Libro, pr.fecha_devolucion_esperada 
     FROM prestamos pr
     INNER JOIN profesores p ON pr.id_profesores = p.id_Profesores
     INNER JOIN libros l ON pr.id_libros = l.id_libro
     ORDER BY pr.id_prestamo DESC'
);

// 5. OBTENER LISTADO DE LIBROS PARA EL MENÚ DESPLEGABLE
$lista_libros = $conexion->query("SELECT id_libro, Titulo FROM libros ORDER BY Titulo ASC");
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
                <label>Nombre del Profesor</label>
                <input type="text" name="Nombre" maxlength="100" required>
            </div>
            <div class="grupo-campo">
                <label>Materia</label>
                <input type="text" name="Materia" maxlength="100" required>
            </div>
            <div class="grupo-campo">
                <label>Cantidad</label>
                <input type="number" name="Cantidad" min="1" value="1" required>
            </div>
            <div class="grupo-campo">
                <label>Fecha de préstamo</label>
                <input type="date" name="Fecha_prestamo" required>
            </div>
            <div class="grupo-campo">
                <label>Seleccionar Libro</label>
                <select name="id_libro" required style="width: 100%; padding: 10px; border-radius: 5px; border: 1px solid #ccc;">
                    <option value="">-- Selecciona un libro disponible --</option>
                    <?php if ($lista_libros && $lista_libros->num_rows > 0): ?>
                        <?php while($lib = $lista_libros->fetch_assoc()): ?>
                            <option value="<?php echo $lib['id_libro']; ?>"><?php echo htmlspecialchars($lib['Titulo']); ?></option>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <option value="">No hay libros registrados en el sistema</option>
                    <?php endif; ?>
                </select>
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
                <th>ID Préstamo</th>
                <th>Nombre</th>
                <th>Materia</th>
                <th>Cantidad</th>
                <th>Fecha de préstamo</th>
                <th>Nombre del Libro</th>
                <th>Fecha de entrega</th>
                <th>Acción</th>
            </tr>
        </thead>
        <tbody>
            <?php 
                if ($resultado && $resultado->num_rows > 0):
                while ($fila = $resultado->fetch_assoc()):
                ?>
                <tr>
                <td><?php echo $fila['id_prestamo']; ?></td>
                <td><?php echo htmlspecialchars($fila['Nombre']); ?></td>
                <td><?php echo htmlspecialchars($fila['Materia']); ?></td>
                <td><?php echo $fila['Cantidad']; ?></td>
                <td><?php echo $fila['fecha_salida']; ?></td>
                <td><?php echo htmlspecialchars($fila['Nombre_del_Libro']); ?></td>
                <td><?php echo $fila['fecha_devolucion_esperada']; ?></td>             
                <td>
                    <button href="profesores.php?eliminar_id=<?php echo $fila['id_prestamo']; ?>" class="btn-eliminar" style="text-decoration: none; display: inline-block; padding: 5px 10px; background-color: #f8d7da; border: 1px solid #f5c6cb; border-radius: 3px; color: #721c24; font-size: 13px;" onclick="return confirm('¿Seguro que deseas eliminar este registro de préstamo de forma permanente?');"></button>
                </td>        
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

    <div class="z1">
        <a class="z2" href="registro.html">REGRESAR</a>
    </div>
</body>
</html>