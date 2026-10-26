<?php
require_once 'conexion.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$id_profesor = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id_profesor) {
    $_SESSION['mensaje'] = 'ID de profesor no válido.';
    $_SESSION['tipoMensaje'] = 'error';
    header("Location: profesores.php");
    exit();
}

// 1. PROCESAR LA ACTUALIZACIÓN (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $Nombre = trim($_POST['Nombre'] ?? '');
    $materias_seleccionadas = $_POST['Materia'] ?? [];
    $Materia = is_array($materias_seleccionadas) ? implode(', ', $materias_seleccionadas) : trim($materias_seleccionadas);

    if ($Nombre === '' || $Materia === '') {
        $mensaje_error = 'Completa todos los campos obligatorios.';
    } else {
        try {
            $stmt_update = $conexion->prepare("UPDATE profesores SET Nombre = ?, Materia = ? WHERE id_Profesores = ?");
            $stmt_update->bind_param("ssi", $Nombre, $Materia, $id_profesor);
            
            if ($stmt_update->execute()) {
                $_SESSION['mensaje'] = 'Docente actualizado correctamente.';
                $_SESSION['tipoMensaje'] = 'exito';
                header("Location: profesores.php");
                exit();
            } else {
                $mensaje_error = 'Error al actualizar el docente.';
            }
            $stmt_update->close();
        } catch (Exception $e) {
            $mensaje_error = 'Error: ' . $e->getMessage();
        }
    }
}

// 2. OBTENER LOS DATOS ACTUALES DEL PROFESOR
$stmt_get = $conexion->prepare("SELECT Nombre, Materia FROM profesores WHERE id_Profesores = ?");
$stmt_get->bind_param("i", $id_profesor);
$stmt_get->execute();
$resultado = $stmt_get->get_result();
$profesor = $resultado->fetch_assoc();
$stmt_get->close();

if (!$profesor) {
    $_SESSION['mensaje'] = 'El profesor no existe.';
    $_SESSION['tipoMensaje'] = 'error';
    header("Location: profesores.php");
    exit();
}

// 3. CONVERTIR LAS MATERIAS EN ARREGLO PARA LOS CHECKBOXES
$materias_actuales = array_map('trim', explode(',', $profesor['Materia']));
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Profesor</title>
    <link rel="stylesheet" href="style.css?v=<?php echo time(); ?>">

</head>
<body class="body2">

    <?php if (isset($mensaje_error)): ?>
    <div class="alerta error" style="padding: 10px; margin: 15px auto; max-width: 600px; text-align: center; border-radius: 5px; font-weight: bold; background-color: #f8d7da; color: #721c24;">
        <?php echo $mensaje_error; ?>
    </div>
    <?php endif; ?>

    <div class="contenedor-formulario">
        <h2>Editar Docente</h2>
        
        <form action="editar_profesor.php?id=<?php echo $id_profesor; ?>" method="POST">
            <div class="grupo-campo">
                <label>Nombre del Profesor</label>
                <input type="text" name="Nombre" value="<?php echo htmlspecialchars($profesor['Nombre']); ?>" maxlength="100" required>
            </div>
            
            <div class="contenedor-materias">
                <span class="titulo-seccion">Academia</span>
                <div class="grid-checkboxes">
                    <label class="opcion-checkbox">
                        <input type="checkbox" name="Materia[]" value="Iformatica, Contabilidad y Electronica" <?php echo in_array('
                        Iformatica, Contabilidad y Electronica', $materias_actuales) ? 'checked' : ''; ?>> Informática, Contabilidad y Electrónica
                    </label>
                    <label class="opcion-checkbox">
                        <input type="checkbox" name="Materia[]" value="Gastronomia y Enfermeria" <?php echo in_array('Gastronomia y Enfermeria', $materias_actuales) ? 'checked' : ''; ?>> Gastronomía y Enfermería
                    </label>
                    <label class="opcion-checkbox">
                        <input type="checkbox" name="Materia[]" value="Educacion Fisica Y Artes" <?php echo in_array('Educacion Fisica Y Artes', $materias_actuales) ? 'checked' : ''; ?>> Educacion Fisica Y Artes
                    </label>
                    <label class="opcion-checkbox">
                        <input type="checkbox" name="Materia[]" value="Manufactura del Vestido" <?php echo in_array('Manufactura del Vestido', $materias_actuales) ? 'checked' : ''; ?>> Manufactura del Vestido
                    </label>
                    <label class="opcion-checkbox">
                        <input type="checkbox" name="Materia[]" value="Lengua y Comunicacion" <?php echo in_array('Lengua y Comunicacion', $materias_actuales) ? 'checked' : ''; ?>> Lengua y Comunicación
                    </label>
                </div>
            </div>
            
            <div class="botones-acciones">
                <button type="submit" class="boton-guardar">Actualizar Cambios</button>
                <a href="profesores.php" class="boton-cancelar">Cancelar</a>
            </div>
        </form> 
    </div> 
</body>
</html>