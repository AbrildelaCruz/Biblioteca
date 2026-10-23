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
    $Fecha_de_préstamo = $_POST['Fecha_de_prestamo'] ?? '';
    $id_libro = filter_input(INPUT_POST, 'id_libro', FILTER_VALIDATE_INT);
    $Fecha_de_entrega = $_POST['Fecha_de_entrega'] ?? '';

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
                'INSERT INTO prestamos (id_libro, id_profesores, Cantidad, fecha_salida, fecha_devolucion_esperada) VALUES (?, ?, ?, ?, ?)'
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
     INNER JOIN libros l ON pr.id_libro = l.id_libro
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
    <title>Registro de profesores</title>
    <link rel="stylesheet" href="style.css?v=<?php echo time(); ?>">
    <link rel="icon" type="image/png" href="logobiblioteca.png">
    <link rel="stylesheet" href="https://jsdelivr.net"/>

<style>
        /* Ajuste opcional para que combine perfectamente con tus colores morados */
        .choices__list--multiple .choices__item {
            background-color: #4a148c !important; /* Tu color morado */
            border: 1px solid #4a148c !important;
            border-radius: 4px !important;
        }
        .choices__inner {
            border-radius: 5px !important;
            border: 1px solid #ccc !important;
            background-color: #fff !important;
        }
    </style>

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
        <h2>Registrar Docente</h2>
        
        <form action="profesores.php" method="POST">
            <div class="grupo-campo">
                <label>Nombre del Profesor</label>
                <input type="text" name="Nombre" maxlength="100" required>
            </div>
            <div class="grupo-campo">
                <label>Materia</label>
                <input type="text" name="Materia" maxlength="100" required>
                <div class="grupo-campo">
    <!-- Asegúrate de tener el id="materias" y los corchetes [] en el name -->
    <select name="Materia[]" id="materias" multiple required>
        <option value="Conciencia Historica">Conciencia Historica</option>
        <option value="Filosofia/Humanidades">Filosofia/Humanidades</option>
        <option value="Natacion">Natacion</option>
        <option value="Matematicas">Matematicas</option>
        <option value="Lengua y Comunicacion">Lengua y Comunicacion</option>
    </select>
    </div>
    <!-- ENLACES NUEVOS: Script de Choices.js e inicialización -->
    <script src="https://jsdelivr.net"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const elementoSelect = document.getElementById('materias');
            new Choices(elementoSelect, {
                removeItemButton: true, // Agrega la 'X' para quitar materias seleccionadas
                noChoicesText: 'No hay más materias disponibles',
                itemSelectText: 'Presiona para seleccionar',
                placeholder: true,
                placeholderValue: '-- Selecciona las materias --'
            });
        });
    </script>
            </div>
            <button type="submit" class="boton-guardar">Guardar Profesor</button>
        </form> 
           </div> 

</head>
<body>
    <div class="z1">
        <a class="z2" href="registro.html">REGRESAR</a>
    </div>
</body>
</html>