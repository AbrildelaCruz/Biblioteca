<?php
require_once 'conexion.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$mensaje = $_SESSION['mensaje'] ?? '';
$tipoMensaje = $_SESSION['tipoMensaje'] ?? '';
unset($_SESSION['mensaje'], $_SESSION['tipoMensaje']);

// 1. PROCESAR EL FORMULARIO POR MÉTODO POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $Nombre = trim($_POST['Nombre'] ?? '');
    $materias_seleccionadas = $_POST['Materia'] ?? [];
    $Materia = is_array($materias_seleccionadas) ? implode(', ', $materias_seleccionadas) : trim($materias_seleccionadas);

    if ($Nombre === '' || $Materia === '') {
        $_SESSION['mensaje'] = 'Completa todos los campos obligatorios (Nombre y Materias).';
        $_SESSION['tipoMensaje'] = 'error';
    } else {
        try {
            $stmt_prof = $conexion->prepare("SELECT id_Profesores FROM profesores WHERE Nombre = ? AND Materia = ?");
            $stmt_prof->bind_param("ss", $Nombre, $Materia);
            $stmt_prof->execute();
            $res_prof = $stmt_prof->get_result();
            
            if ($res_prof->num_rows > 0) {
                $_SESSION['mensaje'] = 'Este docente ya se encuentra registrado con las mismas materias.';
                $_SESSION['tipoMensaje'] = 'error';
            } else {
                $stmt_ins_prof = $conexion->prepare("INSERT INTO profesores (Nombre, Materia) VALUES (?, ?)");
                $stmt_ins_prof->bind_param("ss", $Nombre, $Materia);
                $stmt_ins_prof->execute();
                $stmt_ins_prof->close();
                
                $_SESSION['mensaje'] = 'Docente registrado correctamente.';
                $_SESSION['tipoMensaje'] = 'exito';
            }
            $stmt_prof->close();

        } catch (Exception $e) {
            $_SESSION['mensaje'] = 'Error al registrar el docente: ' . $e->getMessage();
            $_SESSION['tipoMensaje'] = 'error';
        }
    }
    header("Location: ver_profesores.php"); // Redirige directo a la tabla externa al guardar
    exit();
}

// 2. ACCIÓN DE ELIMINAR DOCENTE (GET DE REGRESO)
if (isset($_GET['eliminar_id'])) {
    $id_a_borrar = filter_input(INPUT_GET, 'eliminar_id', FILTER_VALIDATE_INT);
    if ($id_a_borrar) {
        $stmt_borrar = $conexion->prepare('DELETE FROM profesores WHERE id_Profesores = ?');
        if ($stmt_borrar) {
            $stmt_borrar->bind_param('i', $id_a_borrar);
            if ($stmt_borrar->execute()) {
                $_SESSION['mensaje'] = 'Docente eliminado correctamente.';
                $_SESSION['tipoMensaje'] = 'exito';
            } else {
                $_SESSION['mensaje'] = 'Error al eliminar el docente de la base de datos.';
                $_SESSION['tipoMensaje'] = 'error';
            }
            $stmt_borrar->close();
        }
    }
    header("Location: ver_profesores.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de profesores</title>
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
        <h2>Registrar Docente</h2>
        
        <form action="profesores.php" method="POST">
            <div class="grupo-campo">
                <label>Nombre del Profesor</label>
                <input type="text" name="Nombre" maxlength="100" required>
            </div>
            
            <div class="contenedor-materias">
                <span class="titulo-seccion">Academia</span>
                <div class="grid-checkboxes">
                    <label class="opcion-checkbox">
                        <input type="checkbox" name="Materia[]" value="Iformatica, Contabilidad y Electronica"> Informática/Contabilidad/Electrónica
                    </label>
                    <label class="opcion-checkbox">
                        <input type="checkbox" name="Materia[]" value="Gastronomia y Enfermeria"> Gastronomía/Enfermería
                    </label>
                    <label class="opcion-checkbox">
                        <input type="checkbox" name="Materia[]" value="Educacion Fisica Y Artes"> Educación Física/Artes
                    </label>
                    <label class="opcion-checkbox">
                        <input type="checkbox" name="Materia[]" value="Manufactura del Vestido"> Manufactura del Vestido
                    </label>
                    <label class="opcion-checkbox">
                        <input type="checkbox" name="Materia[]" value="Lengua y Comunicacion"> Lengua y Comunicación
                    </label>
                </div>
            </div>
            
            <button type="submit" class="boton-guardar">Guardar Docente</button>
        </form> 
        
        <!-- Enlace agregado para ir a la nueva página de la tabla -->
        <a href="ver_profesores.php" style="display: block; text-align: center; margin-top: 20px; font-weight: bold; color: white; text-decoration: none; background-color: #28a745; padding: 10px; border-radius: 5px;">🔍 Ver Profesores Registrados</a>
    </div> 

    <div class="z1" style="text-align: center; margin-top: 20px; margin-bottom: 40px;">
        <a class="z2" href="registro.html" style="text-decoration: none; font-weight: bold;">REGRESAR</a>
    </div>
</body>
</html>