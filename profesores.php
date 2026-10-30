<?php
require_once 'conexion.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$mensaje = $_SESSION['mensaje'] ?? '';
$tipoMensaje = $_SESSION['tipoMensaje'] ?? '';
unset($_SESSION['mensaje'], $_SESSION['tipoMensaje']);

// Inicializar variables para el formulario
$nombre_actual = "";
$materias_actuales = "";
$es_edicion = isset($_GET['editar_id']);
$id_editar = $es_edicion ? filter_input(INPUT_GET, 'editar_id', FILTER_VALIDATE_INT) : null;

// 1. PROCESAR EL FORMULARIO POR MÉTODO POST (GUARDAR / ACTUALIZAR)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $Nombre = trim($_POST['Nombre'] ?? '');
    $materias_seleccionadas = $_POST['Materia'] ?? [];
    $Materia = is_array($materias_seleccionadas) ? implode(', ', $materias_seleccionadas) : trim($materias_seleccionadas);

    // Recuperar ID oculto en caso de que estemos editando
    $id_docente_post = filter_input(INPUT_POST, 'id_docente', FILTER_VALIDATE_INT);

    if ($Nombre === '' || $Materia === '') {
        $_SESSION['mensaje'] = 'Completa todos los campos obligatorios (Nombre y Materias).';
        $_SESSION['tipoMensaje'] = 'error';
    } else {
        try {
            if ($id_docente_post) {
                // ACCIÓN: ACTUALIZAR REGISTRO EXISTENTE
                $stmt_upd = $conexion->prepare("UPDATE profesores SET Nombre = ?, Materia = ? WHERE id_profesores = ?");
                $stmt_upd->bind_param("ssi", $Nombre, $Materia, $id_docente_post);
                $stmt_upd->execute();
                $stmt_upd->close();

                $_SESSION['mensaje'] = 'Docente actualizado correctamente.';
                $_SESSION['tipoMensaje'] = 'exito';
            } else {
                // ACCIÓN: INSERTAR REGISTRO NUEVO
                $stmt_prof = $conexion->prepare("SELECT id_profesores FROM profesores WHERE Nombre = ? AND Materia = ?");
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
            }

        } catch (Exception $e) {
            $_SESSION['mensaje'] = 'Error al procesar el docente: ' . $e->getMessage();
            $_SESSION['tipoMensaje'] = 'error';
        }
    }
    header("Location: ver_profesores.php");
    exit();
}

// 2. ACCIÓN DE ELIMINAR DOCENTE
if (isset($_GET['eliminar_id'])) {
    $id_a_borrar = filter_input(INPUT_GET, 'eliminar_id', FILTER_VALIDATE_INT);
    if ($id_a_borrar) {
        $stmt_borrar = $conexion->prepare('DELETE FROM profesores WHERE id_profesores = ?');
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

// 3. CONSULTAR DATOS DEL DOCENTE SI ES EDICIÓN (MÉTODO GET)
if ($es_edicion && $id_editar) {
    try {
        $stmt_carga = $conexion->prepare("SELECT Nombre, Materia FROM profesores WHERE id_profesores = ?");
        $stmt_carga->bind_param("i", $id_editar);
        $stmt_carga->execute();
        $res_carga = $stmt_carga->get_result();

        if ($row = $res_carga->fetch_assoc()) {
            $nombre_actual = $row['Nombre'];
            $materias_actuales = $row['Materia'];
        }
        $stmt_carga->close();
    } catch (Exception $e) {
        $mensaje = 'Error al cargar los datos del docente.';
        $tipoMensaje = 'error';
    }
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $es_edicion ? 'Editar profesor' : 'Registro de profesores'; ?></title>
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
        <div class="alerta"
            style="padding: 12px; margin: 20px auto; max-width: 700px; text-align: center; border-radius: 8px; font-weight: bold; box-shadow: 0 4px 12px rgba(0,0,0,0.05); background-color: <?php echo $tipoMensaje === 'exito' ? '#E8F5E9' : '#FFEBEE'; ?>; color: <?php echo $tipoMensaje === 'exito' ? '#2E7D32' : '#C62828'; ?>; border: 1px solid <?php echo $tipoMensaje === 'exito' ? '#C8E6C9' : '#FFCDD2'; ?>;">
            <?php echo $mensaje; ?>
        </div>
    <?php endif; ?>

    <div class="contenedor-formulario">
        <h2><?php echo $es_edicion ? 'Editar Docente' : 'Registrar Docente'; ?></h2>

        <form action="profesores.php" method="POST">
            <?php if ($es_edicion): ?>
                <input type="hidden" name="id_docente" value="<?php echo $id_editar; ?>">
            <?php endif; ?>

            <div class="grupo-campo">
                <label>Nombre del Docente</label>
                <!-- Le añadimos el id="inputNombre" para poder leerlo desde JavaScript -->
                <input type="text" id="inputNombre" name="Nombre"
                    value="<?php echo htmlspecialchars($nombre_actual); ?>" maxlength="100" required>

                <!-- BOTÓN INTERACTIVO DE BÚSQUEDA EN TIEMPO REAL -->
                <button type="button" class="boton-buscar-inline" onclick="buscarDocente()">
                    🔍 Buscar Docente
                </button>
            </div>

            <div class="contenedor-materias">
                <span class="titulo-seccion">Academia</span>
                <div class="grid-checkboxes">
                    <label class="opcion-checkbox">
                        <input type="checkbox" name="Materia[]" value="Informatica, Contabilidad y Electronica" <?php if (strpos($materias_actuales, 'Iformatica') !== false)
                            echo 'checked'; ?>>
                        Informática/Contabilidad/Electrónica
                    </label>
                    <label class="opcion-checkbox">
                        <input type="checkbox" name="Materia[]" value="Gastronomia y Enfermeria" <?php if (strpos($materias_actuales, 'Gastronomia') !== false)
                            echo 'checked'; ?>>
                        Gastronomía/Enfermería
                    </label>
                    <label class="opcion-checkbox">
                        <input type="checkbox" name="Materia[]" value="Educacion Fisica Y Artes" <?php if (strpos($materias_actuales, 'Educacion Fisica') !== false)
                            echo 'checked'; ?>> Educación
                        Física/Artes
                    </label>
                    <label class="opcion-checkbox">
                        <input type="checkbox" name="Materia[]" value="Manufactura del Vestido" <?php if (strpos($materias_actuales, 'Manufactura') !== false)
                            echo 'checked'; ?>> Manufactura del
                        Vestido
                    </label>
                    <label class="opcion-checkbox">
                        <input type="checkbox" name="Materia[]" value="Lengua y Comunicacion" <?php if (strpos($materias_actuales, 'Lengua') !== false)
                            echo 'checked'; ?>> Lengua y Comunicación
                    </label>
                </div>
            </div>

            <button type="submit"
                class="boton-guardar"><?php echo $es_edicion ? 'Guardar Cambios' : 'Guardar Docente'; ?></button>
        </form>

        <a href="ver_profesores.php"
            style="display: block; text-align: center; margin-top: 20px; font-weight: 600; color: white; text-decoration: none; background-color: #2A9D8F; padding: 12px; border-radius: 8px; transition: background-color 0.2s;"
            onmouseover="this.style.backgroundColor='#218074'" onmouseout="this.style.backgroundColor='#2A9D8F'">🔍 Ver
            Profesores Registrados</a>
    </div>

    <div class="z1" style="text-align: center; margin-top: 20px; margin-bottom: 40px;">
        <a class="z2" href="registro.html" style="text-decoration: none; font-weight: bold;">REGRESAR</a>
    </div>

    <!-- SCRIPT DE JAVASCRIPT PARA PROCESAR LA BÚSQUEDA DINÁMICA -->
    <script>
        function buscarDocente() {
            // 1. Obtenemos el elemento input
            var inputElement = document.getElementById('inputNombre');

            // Verificación de seguridad por si no encuentra el ID
            if (!inputElement) {
                console.error("No se encontró el elemento con ID 'inputNombre'");
                return;
            }

            var nombreEscrito = inputElement.value.trim();

            // 2. Si está vacío, le avisamos al usuario
            if (nombreEscrito === "") {
                alert("Por favor, escribe el nombre de un docente para poder buscarlo.");
                inputElement.focus();
                return;
            }

            // 3. Redirección directa hacia la tabla con el parámetro de búsqueda
            window.location.href = "ver_profesores.php?buscar=" + encodeURIComponent(nombreEscrito);
        }
    </script>

</html>