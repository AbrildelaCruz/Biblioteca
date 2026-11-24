<?php
session_start();
if (!isset($_SESSION['admin_autenticado']) || $_SESSION['admin_autenticado'] !== true) {
    header("Location: login.php");
    exit();
}
require_once 'conexion.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$mensaje = $_SESSION['mensaje'] ?? '';
$tipoMensaje = $_SESSION['tipoMensaje'] ?? '';
unset($_SESSION['mensaje'], $_SESSION['tipoMensaje']);

$modo_editar = false;
$rol_editar = '';
$id_editar = '';

$nombre = '';
$apellido_paterno = '';
$apellido_materno = '';
$madre_encargada = '';
$familia = '';
$piso = '';
$grupo = '';
$materias_guardadas = [];

// 1. DETECTAR MODO EDICIÓN (Compatible con ver_alumnos.php y ver_profesores.php)
if (isset($_GET['editar_rol'], $_GET['editar_id'])) {
    $rol_editar = $_GET['editar_rol'];
    $id_editar = filter_input(INPUT_GET, 'editar_id', FILTER_VALIDATE_INT);

    if ($id_editar && $rol_editar === 'alumno') {
        $stmt = $conexion->prepare("SELECT * FROM alumnos WHERE id_Alumnos = ?");
        $stmt->bind_param("i", $id_editar);
        $stmt->execute();
        if ($fila = $stmt->get_result()->fetch_assoc()) {
            $modo_editar = true;
            $nombre = $fila['Nombre'];
            $apellido_paterno = $fila['Apellido_Paterno'] ?? '';
            $apellido_materno = $fila['Apellido_Materno'] ?? '';
            $madre_encargada = $fila['Madre_encargada'] ?? '';
            $familia = $fila['Familia'] ?? '';
            $piso = $fila['Piso'] ?? '';
            $grupo = $fila['Grupo'] ?? '';
        }
        $stmt->close();
    } elseif ($id_editar && $rol_editar === 'profesor') {
        $stmt = $conexion->prepare("SELECT * FROM profesores WHERE id_profesores = ?");
        $stmt->bind_param("i", $id_editar);
        $stmt->execute();
        if ($fila = $stmt->get_result()->fetch_assoc()) {
            $modo_editar = true;
            $nombre = $fila['Nombre'];
            $apellido_paterno = $fila['Apellido_Paterno'] ?? '';
            $apellido_materno = $fila['Apellido_Materno'] ?? '';
            $materias_guardadas = array_map('trim', explode(',', $fila['Materia'] ?? ''));
        }
        $stmt->close();
    }
}

// 2. DETECTAR ELIMINACIÓN
if (isset($_GET['eliminar_rol'], $_GET['eliminar_id'])) {
    $rol_del = $_GET['eliminar_rol'];
    $id_del = filter_input(INPUT_GET, 'eliminar_id', FILTER_VALIDATE_INT);

    if ($id_del && $rol_del === 'alumno') {
        $stmt = $conexion->prepare("DELETE FROM alumnos WHERE id_Alumnos = ?");
        $stmt->bind_param("i", $id_del);
        if ($stmt->execute()) {
            $_SESSION['mensaje'] = "✅ Alumno eliminado correctamente.";
            $_SESSION['tipoMensaje'] = "exito";
        } else {
            $_SESSION['mensaje'] = "❌ No se puede eliminar porque tiene préstamos registrados.";
            $_SESSION['tipoMensaje'] = "error";
        }
        $stmt->close();
        header("Location: ver_alumnos.php");
        exit();
    } elseif ($id_del && $rol_del === 'profesor') {
        $stmt = $conexion->prepare("DELETE FROM profesores WHERE id_profesores = ?");
        $stmt->bind_param("i", $id_del);
        if ($stmt->execute()) {
            $_SESSION['mensaje'] = "✅ Profesor eliminado correctamente.";
            $_SESSION['tipoMensaje'] = "exito";
        } else {
            $_SESSION['mensaje'] = "❌ No se puede eliminar porque tiene préstamos registrados.";
            $_SESSION['tipoMensaje'] = "error";
        }
        $stmt->close();
        header("Location: ver_profesores.php");
        exit();
    }
}

// 3. PROCESAR FORMULARIO (REGISTRAR O ACTUALIZAR)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $rol = trim($_POST['rol'] ?? '');
    $id_post = filter_input(INPUT_POST, 'id_editar', FILTER_VALIDATE_INT);

    $nombre = trim($_POST['Nombre'] ?? '');
    $apellido_paterno = trim($_POST['Apellido_Paterno'] ?? '');
    $apellido_materno = trim($_POST['Apellido_Materno'] ?? '');

    if ($rol === '' || $nombre === '' || $apellido_paterno === '') {
        $_SESSION['mensaje'] = "⚠️ Selecciona el rol y llena al menos Nombre y Apellido Paterno.";
        $_SESSION['tipoMensaje'] = "error";
        header("Location: registrar_usuario.php");
        exit();
    }

    if ($rol === 'alumno') {
        $madre_encargada = trim($_POST['Madre_encargada'] ?? '');
        $familia = trim($_POST['Familia'] ?? '');
        $piso = trim($_POST['Piso'] ?? '');
        $grupo = trim($_POST['Grupo'] ?? '');

        // Verificar si ya está registrado (Nombre + Apellido Paterno + Apellido Materno)
        if ($id_post) {
            $stmt_dup = $conexion->prepare("SELECT id_Alumnos FROM alumnos WHERE Nombre = ? AND Apellido_Paterno = ? AND Apellido_Materno = ? AND id_Alumnos != ?");
            $stmt_dup->bind_param("sssi", $nombre, $apellido_paterno, $apellido_materno, $id_post);
        } else {
            $stmt_dup = $conexion->prepare("SELECT id_Alumnos FROM alumnos WHERE Nombre = ? AND Apellido_Paterno = ? AND Apellido_Materno = ?");
            $stmt_dup->bind_param("sss", $nombre, $apellido_paterno, $apellido_materno);
        }
        $stmt_dup->execute();
        $ya_existe = $stmt_dup->get_result()->num_rows > 0;
        $stmt_dup->close();

        if ($ya_existe) {
            $_SESSION['mensaje'] = "⚠️ El alumno '$nombre $apellido_paterno $apellido_materno' ya se encuentra registrado en el sistema.";
            $_SESSION['tipoMensaje'] = "error";
            header("Location: registrar_usuario.php" . ($id_post ? "?editar_rol=alumno&editar_id=$id_post" : ""));
            exit();
        }

        if ($id_post) {
            $stmt = $conexion->prepare("UPDATE alumnos SET Nombre = ?, Apellido_Paterno = ?, Apellido_Materno = ?, Madre_encargada = ?, Familia = ?, Piso = ?, Grupo = ? WHERE id_Alumnos = ?");
            $stmt->bind_param("sssssssi", $nombre, $apellido_paterno, $apellido_materno, $madre_encargada, $familia, $piso, $grupo, $id_post);
            $msg_ok = "✅ Alumno actualizado correctamente.";
        } else {
            $res_max = $conexion->query("SELECT IFNULL(MAX(id_Alumnos), 0) + 1 AS sig FROM alumnos");
            $sig = $res_max->fetch_assoc()['sig'];
            $matricula = 'SUL-26-' . str_pad($sig, 3, '0', STR_PAD_LEFT);

            $stmt = $conexion->prepare("INSERT INTO alumnos (Matricula, Nombre, Apellido_Paterno, Apellido_Materno, Madre_encargada, Familia, Piso, Grupo) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssssssss", $matricula, $nombre, $apellido_paterno, $apellido_materno, $madre_encargada, $familia, $piso, $grupo);
            $msg_ok = "✅ Alumno registrado correctamente con matrícula $matricula.";
        }

        if ($stmt && $stmt->execute()) {
            $_SESSION['mensaje'] = $msg_ok;
            $_SESSION['tipoMensaje'] = "exito";
            $stmt->close();
            header("Location: ver_alumnos.php");
            exit();
        } else {
            $_SESSION['mensaje'] = "❌ Error al guardar alumno: " . $conexion->error;
            $_SESSION['tipoMensaje'] = "error";
            header("Location: registrar_usuario.php");
            exit();
        }

    } elseif ($rol === 'profesor') {
        $materias_arr = $_POST['Materia'] ?? [];
        $materia = is_array($materias_arr) ? implode(', ', $materias_arr) : trim($materias_arr);

        if ($materia === '') {
            $_SESSION['mensaje'] = "⚠️ Selecciona al menos una Academia para el docente.";
            $_SESSION['tipoMensaje'] = "error";
            header("Location: registrar_usuario.php");
            exit();
        }

        // Verificar si ya está registrado (Nombre + Apellido Paterno + Apellido Materno)
        if ($id_post) {
            $stmt_dup = $conexion->prepare("SELECT id_profesores FROM profesores WHERE Nombre = ? AND Apellido_Paterno = ? AND Apellido_Materno = ? AND id_profesores != ?");
            $stmt_dup->bind_param("sssi", $nombre, $apellido_paterno, $apellido_materno, $id_post);
        } else {
            $stmt_dup = $conexion->prepare("SELECT id_profesores FROM profesores WHERE Nombre = ? AND Apellido_Paterno = ? AND Apellido_Materno = ?");
            $stmt_dup->bind_param("sss", $nombre, $apellido_paterno, $apellido_materno);
        }
        $stmt_dup->execute();
        $ya_existe = $stmt_dup->get_result()->num_rows > 0;
        $stmt_dup->close();

        if ($ya_existe) {
            $_SESSION['mensaje'] = "⚠️ El docente '$nombre $apellido_paterno $apellido_materno' ya se encuentra registrado en el sistema.";
            $_SESSION['tipoMensaje'] = "error";
            header("Location: registrar_usuario.php" . ($id_post ? "?editar_rol=profesor&editar_id=$id_post" : ""));
            exit();
        }

        if ($id_post) {
            $stmt = $conexion->prepare("UPDATE profesores SET Nombre = ?, Apellido_Paterno = ?, Apellido_Materno = ?, Materia = ? WHERE id_profesores = ?");
            $stmt->bind_param("ssssi", $nombre, $apellido_paterno, $apellido_materno, $materia, $id_post);
            $msg_ok = "✅ Profesor actualizado correctamente.";
        } else {
            $res_max = $conexion->query("SELECT IFNULL(MAX(id_profesores), 0) + 1 AS sig FROM profesores");
            $sig = $res_max->fetch_assoc()['sig'];
            $matricula = 'DOC-26-' . str_pad($sig, 3, '0', STR_PAD_LEFT);

            $stmt = $conexion->prepare("INSERT INTO profesores (Matricula, Nombre, Apellido_Paterno, Apellido_Materno, Materia) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("sssss", $matricula, $nombre, $apellido_paterno, $apellido_materno, $materia);
            $msg_ok = "✅ Profesor registrado correctamente con matrícula $matricula.";
        }

        if ($stmt && $stmt->execute()) {
            $_SESSION['mensaje'] = $msg_ok;
            $_SESSION['tipoMensaje'] = "exito";
            $stmt->close();
            header("Location: ver_profesores.php");
            exit();
        } else {
            $_SESSION['mensaje'] = "❌ Error al guardar profesor: " . $conexion->error;
            $_SESSION['tipoMensaje'] = "error";
            header("Location: registrar_usuario.php");
            exit();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Usuarios - Biblioteca Sullivan</title>
    <link rel="stylesheet" href="style.css">
    <link rel="icon" type="image/png" href="logobiblioteca.png">
</head>
<body class="body2">

    <?php if ($mensaje !== ''): ?>
        <div class="alerta <?php echo $tipoMensaje; ?>" style="padding: 12px; margin: 15px auto; max-width: 750px; text-align: center; border-radius: 8px; font-weight: bold; background-color: <?php echo $tipoMensaje === 'exito' ? '#d4edda' : '#f8d7da'; ?>; color: <?php echo $tipoMensaje === 'exito' ? '#155724' : '#721c24'; ?>;">
            <?php echo $mensaje; ?>
        </div>
    <?php endif; ?>

    <div class="contenedor-formulario" style="max-width: 780px;">
        <h2><?php echo $modo_editar ? '📝 Modificar Usuario' : '👥 Registro de Usuarios'; ?></h2>

        <form method="POST" action="registrar_usuario.php" style="display: flex; flex-direction: column; gap: 16px;">
            <input type="hidden" name="id_editar" value="<?php echo $id_editar; ?>">

            <div class="grupo-campo">
                <label>Rol del Usuario *</label>
                <select name="rol" id="select_rol" required onchange="alternarCamposRol()" <?php echo $modo_editar ? 'style="pointer-events:none; background:#F1F5F9;"' : ''; ?>>
                    <option value="">-- Selecciona Alumno o Profesor --</option>
                    <option value="alumno" <?php echo ($rol_editar === 'alumno') ? 'selected' : ''; ?>>Alumno</option>
                    <option value="profesor" <?php echo ($rol_editar === 'profesor') ? 'selected' : ''; ?>>Profesor</option>
                </select>
            </div>

            <!-- Nombre dividido en 3 campos para ambos roles -->
            <div class="grid-dos-columnas" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(210px, 1fr)); gap: 15px;">
                <div class="grupo-campo">
                    <label>Nombre(s) *</label>
                    <input type="text" name="Nombre" value="<?php echo htmlspecialchars($nombre); ?>" maxlength="100" required>
                </div>
                <div class="grupo-campo">
                    <label>Apellido Paterno *</label>
                    <input type="text" name="Apellido_Paterno" value="<?php echo htmlspecialchars($apellido_paterno); ?>" maxlength="100" required>
                </div>
                <div class="grupo-campo">
                    <label>Apellido Materno</label>
                    <input type="text" name="Apellido_Materno" value="<?php echo htmlspecialchars($apellido_materno); ?>" maxlength="100">
                </div>
            </div>

            <!-- Campos exclusivos de Alumno -->
            <div id="campos_alumno" style="display: <?php echo ($rol_editar === 'alumno') ? 'grid' : 'none'; ?>; grid-template-columns: 1fr 1fr; gap: 15px;">
                <div class="grupo-campo">
                    <label>Madre Encargada</label>
                    <input type="text" name="Madre_encargada" value="<?php echo htmlspecialchars($madre_encargada); ?>" maxlength="100">
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

            <!-- Campos exclusivos de Profesor -->
            <div id="campos_profesor" style="display: <?php echo ($rol_editar === 'profesor') ? 'block' : 'none'; ?>;">
                <label style="font-weight: 600; font-size: 14px; color: #1E293B; display: block; margin-bottom: 8px;">Academia / Materia(s) *</label>
                <div class="grid-checkboxes">
                    <?php
                    $academias = [
                        'Informatica, Contabilidad y Electronica',
                        'Gastronomia y Enfermeria',
                        'Educacion Fisica Y Artes',
                        'Manufactura del Vestido',
                        'Lengua y Comunicacion'
                    ];
                    foreach ($academias as $ac):
                        $checked = in_array($ac, $materias_guardadas) ? 'checked' : '';
                    ?>
                        <label class="opcion-checkbox">
                            <input type="checkbox" name="Materia[]" value="<?php echo $ac; ?>" <?php echo $checked; ?>> <?php echo $ac; ?>
                        </label>
                    <?php endforeach; ?>
                </div>
            </div>

            <button type="submit" class="boton-guardar" style="width: 100%; margin-top: 10px;">
                <?php echo $modo_editar ? 'Actualizar Usuario' : 'Guardar Usuario'; ?>
            </button>

            <?php if ($modo_editar): ?>
                <a href="<?php echo ($rol_editar === 'alumno') ? 'ver_alumnos.php' : 'ver_profesores.php'; ?>" style="text-align: center; color: #DC3545; font-weight: bold; text-decoration: none;">❌ Cancelar Edición</a>
            <?php endif; ?>
        </form>

        <div style="display: flex; gap: 10px; flex-wrap: wrap; margin-top: 20px;">
            <a href="ver_alumnos.php" style="flex: 1; text-align: center; background-color: #0F4C81; color: white; padding: 10px; border-radius: 6px; text-decoration: none; font-weight: bold;">👥 Ver Alumnos</a>
            <a href="ver_profesores.php" style="flex: 1; text-align: center; background-color: #28a745; color: white; padding: 10px; border-radius: 6px; text-decoration: none; font-weight: bold;">👨‍🏫 Ver Profesores</a>
        </div>
    </div>

    <div class="z1">
        <a class="z2" href="registro.php">REGRESAR</a>
    </div>

    <script>
    function alternarCamposRol() {
        const rol = document.getElementById('select_rol').value;
        document.getElementById('campos_alumno').style.display = (rol === 'alumno') ? 'grid' : 'none';
        document.getElementById('campos_profesor').style.display = (rol === 'profesor') ? 'block' : 'none';
    }
    </script>
</body>
</html>