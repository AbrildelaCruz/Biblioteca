<?php
require_once 'conexion.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$mensaje = $_SESSION['mensaje'] ?? '';
$tipoMensaje = $_SESSION['tipoMensaje'] ?? '';
unset($_SESSION['mensaje'], $_SESSION['tipoMensaje']);

$modo_editar = false;
$rol_editar = $_GET['editar_rol'] ?? '';
$id_editar = filter_input(INPUT_GET, 'editar_id', FILTER_VALIDATE_INT);

$matricula = '';
$nombre = '';
$madreEncargada = '';
$familia = '';
$piso = '';
$grupo = '';
$materias_actuales = '';

// ==========================================
// 0. VERIFICAR SI SE PIDIÓ ELIMINAR UN REGISTRO
// ==========================================
if (isset($_GET['eliminar_rol']) && isset($_GET['eliminar_id'])) {
    $rol_eliminar = $_GET['eliminar_rol'];
    $id_eliminar = filter_input(INPUT_GET, 'eliminar_id', FILTER_VALIDATE_INT);

    if ($id_eliminar && ($rol_eliminar === 'alumno' || $rol_eliminar === 'profesor')) {
        try {
            if ($rol_eliminar === 'alumno') {
                $stmt_del = $conexion->prepare("DELETE FROM alumnos WHERE id_Alumnos = ?");
                $pagina_destino = "ver_alumnos.php";
            } else {
                $stmt_del = $conexion->prepare("DELETE FROM profesores WHERE id_profesores = ?");
                $pagina_destino = "ver_profesores.php";
            }
            $stmt_del->bind_param("i", $id_eliminar);
            $stmt_del->execute();
            $stmt_del->close();

            header("Location: " . $pagina_destino);
            exit();
        } catch (mysqli_sql_exception $e) {
            $mensaje = "❌ No se puede eliminar este usuario porque tiene préstamos registrados en la bitácora.";
            $tipoMensaje = "error";
        }
    }
}

// 1. CARGAR DATOS SI Vamos a EDITAR
if ($id_editar && ($rol_editar === 'alumno' || $rol_editar === 'profesor')) {
    if ($rol_editar === 'alumno') {
        $stmt = $conexion->prepare("SELECT * FROM alumnos WHERE id_Alumnos = ?");
        $stmt->bind_param("i", $id_editar);
        $stmt->execute();
        if ($fila = $stmt->get_result()->fetch_assoc()) {
            $modo_editar = true;
            $matricula = $fila['Matricula'] ?? '';
            $nombre = $fila['Nombre'];
            $madreEncargada = $fila['Madre_encargada'] ?? '';
            $familia = $fila['Familia'] ?? '';
            $piso = $fila['Piso'] ?? '';
            $grupo = $fila['Grupo'] ?? '';
        }
        $stmt->close();
    } else {
        $stmt = $conexion->prepare("SELECT * FROM profesores WHERE id_profesores = ?");
        $stmt->bind_param("i", $id_editar);
        $stmt->execute();
        if ($fila = $stmt->get_result()->fetch_assoc()) {
            $modo_editar = true;
            $matricula = 'DOC-26-' . str_pad($fila['id_profesores'], 3, '0', STR_PAD_LEFT);
            $nombre = $fila['Nombre'];
            $materias_actuales = $fila['Materia'] ?? '';
        }
        $stmt->close();
    }
}

// 2. PROCESAR REGISTRO O ACTUALIZACIÓN (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $rol = trim($_POST['rol_usuario'] ?? '');
    $id_post = filter_input(INPUT_POST, 'id_usuario', FILTER_VALIDATE_INT);
    $nombre = trim($_POST['Nombre'] ?? '');

    if ($nombre === '' || ($rol !== 'alumno' && $rol !== 'profesor')) {
        $_SESSION['mensaje'] = '⚠️ Selecciona un rol y escribe el nombre completo incluyendo apellido o matrícula';
        $_SESSION['tipoMensaje'] = 'error';
        header("Location: registrar_usuario.php");
        exit();
    }

    if ($rol === 'alumno') {
        $madreEncargada = trim($_POST['Madre_encargada'] ?? '');
        $familia = trim($_POST['Familia'] ?? '');
        $piso = trim($_POST['Piso'] ?? '');
        $grupo = trim($_POST['Grupo'] ?? '');

        if ($id_post) {
            $stmt = $conexion->prepare("UPDATE alumnos SET Nombre=?, Madre_encargada=?, Familia=?, Piso=?, Grupo=? WHERE id_Alumnos=?");
            $stmt->bind_param("sssssi", $nombre, $madreEncargada, $familia, $piso, $grupo, $id_post);
            $ok = $stmt->execute();
            $stmt->close();
            $_SESSION['mensaje'] = $ok ? '✅ Datos del alumno actualizados correctamente.' : '❌ Error al actualizar datos del alumno.';
            $_SESSION['tipoMensaje'] = $ok ? 'exito' : 'error';
        } else {
            $res_max = $conexion->query("SELECT IFNULL(MAX(id_Alumnos), 0) + 1 AS sig FROM alumnos");
            $sig = (int)$res_max->fetch_assoc()['sig'];
            $nueva_mat = 'SUL-26-' . str_pad($sig, 3, '0', STR_PAD_LEFT);

            $stmt = $conexion->prepare("INSERT INTO alumnos (Matricula, Nombre, Madre_encargada, Familia, Piso, Grupo) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssssss", $nueva_mat, $nombre, $madreEncargada, $familia, $piso, $grupo);
            $ok = $stmt->execute();
            $stmt->close();
            $_SESSION['mensaje'] = $ok ? "🎉 Alumno registrado con éxito. Matrícula asignada: $nueva_mat" : '❌ Error al registrar alumno.';
            $_SESSION['tipoMensaje'] = $ok ? 'exito' : 'error';
        }

        // Redirige directo a la tabla de alumnos
        header("Location: ver_alumnos.php");
        exit();

    } else {
        // ROL: PROFESOR
        $materias_sel = $_POST['Materia'] ?? [];
        $materia = is_array($materias_sel) ? implode(', ', $materias_sel) : trim($materias_sel);

        if ($materia === '') {
            $_SESSION['mensaje'] = '⚠️ Selecciona al menos una academia o materia para el profesor.';
            $_SESSION['tipoMensaje'] = 'error';
            header("Location: registrar_usuario.php");
            exit();
        }

        if ($id_post) {
            $stmt = $conexion->prepare("UPDATE profesores SET Nombre=?, Materia=? WHERE id_profesores=?");
            $stmt->bind_param("ssi", $nombre, $materia, $id_post);
            $ok = $stmt->execute();
            $stmt->close();
            $_SESSION['mensaje'] = $ok ? '✅ Docente actualizado correctamente.' : '❌ Error al actualizar docente.';
            $_SESSION['tipoMensaje'] = $ok ? 'exito' : 'error';
        } else {
            $stmt = $conexion->prepare("INSERT INTO profesores (Nombre, Materia) VALUES (?, ?)");
            $stmt->bind_param("ss", $nombre, $materia);
            $ok = $stmt->execute();
            $stmt->close();
            $_SESSION['mensaje'] = $ok ? '🎉 Docente registrado correctamente.' : '❌ Error al registrar docente.';
            $_SESSION['tipoMensaje'] = $ok ? 'exito' : 'error';
        }

        // Redirige directo a la tabla de profesores
        header("Location: ver_profesores.php");
        exit();
    }
}
// 3. PROCESAR ELIMINACIÓN DESDE LAS TABLAS
if (isset($_GET['eliminar_id'], $_GET['eliminar_rol'])) {
    $id_del = filter_input(INPUT_GET, 'eliminar_id', FILTER_VALIDATE_INT);
    $rol_del = $_GET['eliminar_rol'];

    if ($id_del && ($rol_del === 'alumno' || $rol_del === 'profesor')) {
        $tabla = ($rol_del === 'alumno') ? 'alumnos' : 'profesores';
        $col_id = ($rol_del === 'alumno') ? 'id_Alumnos' : 'id_profesores';
        $destino = ($rol_del === 'alumno') ? 'ver_alumnos.php' : 'ver_profesores.php';

        $stmt = $conexion->prepare("DELETE FROM $tabla WHERE $col_id = ?");
        $stmt->bind_param("i", $id_del);
        if ($stmt->execute()) {
            $_SESSION['mensaje'] = '🗑️ Registro eliminado correctamente.';
            $_SESSION['tipoMensaje'] = 'exito';
        } else {
            $_SESSION['mensaje'] = '❌ No se pudo eliminar (verifica que no tenga préstamos registrados).';
            $_SESSION['tipoMensaje'] = 'error';
        }
        $stmt->close();
        header("Location: $destino");
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Usuarios - Biblioteca Sullivan</title>
    <link rel="stylesheet" href="style.css?v=<?php echo time(); ?>">
    <link rel="icon" type="image/png" href="logobiblioteca.png">
</head>
<body class="body2">

    <?php if ($mensaje !== ''): ?>
        <div class="alerta <?php echo $tipoMensaje; ?>"
            style="padding: 12px; margin: 15px auto; max-width: 720px; text-align: center; border-radius: 6px; font-weight: bold; background-color: <?php echo $tipoMensaje === 'exito' ? '#d4edda' : '#f8d7da'; ?>; color: <?php echo $tipoMensaje === 'exito' ? '#155724' : '#721c24'; ?>;">
            <?php echo $mensaje; ?>
        </div>
    <?php endif; ?>

    <div class="contenedor-formulario" style="max-width: 720px;">
        <h2><?php echo $modo_editar ? '📝 Editar ' . ucfirst($rol_editar) : '👤 Registro de Usuarios'; ?></h2>

        <form method="POST" action="registrar_usuario.php" style="display: flex; flex-direction: column; gap: 16px;">
            <input type="hidden" name="id_usuario" value="<?php echo $modo_editar ? $id_editar : ''; ?>">

            <!-- 1. SELECTOR DE ROL -->
            <div class="grupo-campo">
                <label>Selecciona el Rol del Usuario *</label>
                <?php if ($modo_editar): ?>
                    <input type="hidden" name="rol_usuario" value="<?php echo htmlspecialchars($rol_editar); ?>">
                    <input type="text" value="<?php echo ($rol_editar === 'alumno') ? '🎓 Alumno' : '👨‍🏫 Profesor'; ?>" readonly>
                <?php else: ?>
                    <select name="rol_usuario" id="selector_rol" required onchange="alternarCamposRol()">
                        <option value="">-- Elige Alumno o Profesor --</option>
                        <option value="alumno">🎓 Alumno</option>
                        <option value="profesor">👨‍🏫 Profesor</option>
                    </select>
                <?php endif; ?>
            </div>

            <!-- 2. DATOS COMUNES -->
            <div id="bloque_datos_comunes" class="grid-dos-columnas" style="display: <?php echo $modo_editar ? 'grid' : 'none'; ?>; grid-template-columns: 1fr 1fr; gap: 15px;">
                <div class="grupo-campo">
                    <label>Matrícula / Clave</label>
                    <input type="text" id="campo_matricula_preview" value="<?php echo $modo_editar ? htmlspecialchars($matricula) : 'Se asignará al guardar'; ?>" readonly>
                </div>
                <div class="grupo-campo">
                    <label>Nombre Completo *</label>
                    <input type="text" name="Nombre" id="input_nombre_usuario" value="<?php echo htmlspecialchars($nombre); ?>" maxlength="100" required>
                </div>
            </div>

            <!-- 3A. CAMPOS DE ALUMNO -->
            <div id="campos_alumno" class="grid-dos-columnas" style="display: <?php echo ($modo_editar && $rol_editar === 'alumno') ? 'grid' : 'none'; ?>; grid-template-columns: 1fr 1fr; gap: 15px; background: #F8FAFC; padding: 15px; border-radius: 8px; border: 1px solid #DDE6ED;">
                <div class="grupo-campo">
                    <label>Madre Encargada</label>
                    <input type="text" name="Madre_encargada" value="<?php echo htmlspecialchars($madreEncargada); ?>" maxlength="100">
                </div>
                <div class="grupo-campo">
                    <label>Familia</label>
                    <input type="text" name="Familia" value="<?php echo htmlspecialchars($familia); ?>" maxlength="100">
                </div>
                <div class="grupo-campo">
                    <label>Piso</label>
                    <input type="text" name="Piso" value="<?php echo htmlspecialchars($piso); ?>" maxlength="20">
                </div>
                <div class="grupo-campo">
                    <label>Grupo</label>
                    <input type="text" name="Grupo" value="<?php echo htmlspecialchars($grupo); ?>" maxlength="20">
                </div>
            </div>

            <!-- 3B. CAMPOS DE PROFESOR -->
            <div id="campos_profesor" style="display: <?php echo ($modo_editar && $rol_editar === 'profesor') ? 'block' : 'none'; ?>; background: #F8FAFC; padding: 15px; border-radius: 8px; border: 1px solid #DDE6ED;">
                    <span class="titulo-seccion" style="display: block; font-weight: bold; color: #0F4C81; margin-bottom: 10px;">Academia / Materia(s) Asignada(s) *</span>
                <div class="grid-checkboxes">
                    <label class="opcion-checkbox">
                        <input type="checkbox" name="Materia[]" value="Informatica, Contabilidad y Electronica" <?php if (stripos($materias_actuales, 'formatica') !== false) echo 'checked'; ?>>
                        Informática, Contabilidad y Electrónica
                    </label>
                    <label class="opcion-checkbox">
                        <input type="checkbox" name="Materia[]" value="Gastronomia y Enfermeria" <?php if (stripos($materias_actuales, 'Gastronomia') !== false) echo 'checked'; ?>>
                        Gastronomía y Enfermería
                    </label>
                    <label class="opcion-checkbox">
                        <input type="checkbox" name="Materia[]" value="Educacion Fisica Y Artes" <?php if (stripos($materias_actuales, 'Educacion Fisica') !== false) echo 'checked'; ?>>
                        Educación Física y Artes
                    </label>
                    <label class="opcion-checkbox">
                        <input type="checkbox" name="Materia[]" value="Manufactura del Vestido" <?php if (stripos($materias_actuales, 'Manufactura') !== false) echo 'checked'; ?>>
                        Manufactura del Vestido
                    </label>
                    <label class="opcion-checkbox">
                        <input type="checkbox" name="Materia[]" value="Lengua y Comunicacion" <?php if (stripos($materias_actuales, 'Lengua') !== false) echo 'checked'; ?>>
                        Lengua y Comunicación
                    </label>
                </div>
            </div>

            <button type="submit" id="btn_guardar_usuario" class="boton-guardar" style="display: <?php echo $modo_editar ? 'block' : 'none'; ?>; width: 100%; margin-top: 10px;">
                <?php echo $modo_editar ? 'Actualizar Datos' : 'Guardar Registro'; ?>
            </button>

            <?php if ($modo_editar): ?>
                <a href="<?php echo ($rol_editar === 'alumno') ? 'ver_alumnos.php' : 'ver_profesores.php'; ?>" style="display: block; text-align: center; color: #991B1B; font-weight: bold; text-decoration: none;">❌ Cancelar Edición</a>
            <?php endif; ?>
        </form>

        <!-- ACCESOS DIRECTOS A LAS TABLAS DE CADA ROL -->
        <div style="display: flex; flex-wrap: wrap; gap: 10px; margin-top: 20px;">
            <a href="ver_alumnos.php" style="flex: 1; min-width: 200px; text-align: center; font-weight: bold; color: white; text-decoration: none; background-color: #28a745; padding: 11px; border-radius: 6px;">
                🎓 Ver Alumnos Registrados
            </a>
            <a href="ver_profesores.php" style="flex: 1; min-width: 200px; text-align: center; font-weight: bold; color: white; text-decoration: none; background-color: #0F4C81; padding: 11px; border-radius: 6px;">
                👨‍🏫 Ver Profesores Registrados
            </a>
        </div>
    </div>

    <div class="z1">
        <a class="z2" href="registro.php">REGRESAR</a>
    </div>

    <script>
    function alternarCamposRol() {
        const rol = document.getElementById('selector_rol').value;
        const bloqueComun = document.getElementById('bloque_datos_comunes');
        const bloqueAl = document.getElementById('campos_alumno');
        const bloqueProf = document.getElementById('campos_profesor');
        const btnGuardar = document.getElementById('btn_guardar_usuario');
        const previewMat = document.getElementById('campo_matricula_preview');

        if (rol === 'alumno') {
            bloqueComun.style.display = 'grid';
            bloqueAl.style.display = 'grid';
            bloqueProf.style.display = 'none';
            btnGuardar.style.display = 'block';
            previewMat.value = 'Automática (SUL-26-XXX)';
            document.getElementById('input_nombre_usuario').focus();
        } else if (rol === 'profesor') {
            bloqueComun.style.display = 'grid';
            bloqueAl.style.display = 'none';
            bloqueProf.style.display = 'block';
            btnGuardar.style.display = 'block';
            previewMat.value = 'Automática (DOC-26-XXX)';
            document.getElementById('input_nombre_usuario').focus();
        } else {
            bloqueComun.style.display = 'none';
            bloqueAl.style.display = 'none';
            bloqueProf.style.display = 'none';
            btnGuardar.style.display = 'none';
        }
    }
    </script>
</body>
</html>