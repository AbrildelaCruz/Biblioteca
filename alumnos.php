<?php
require_once 'conexion.php';

// 1. RECUPERAR MENSAJES DE LA SESIÓN TRAS REDIRECCIÓN
$mensaje = $_SESSION['mensaje'] ?? '';
$tipoMensaje = $_SESSION['tipoMensaje'] ?? '';
unset($_SESSION['mensaje'], $_SESSION['tipoMensaje']);

// Variables en limpio para el formulario
$modo_editar = false;
$id_Alumnos = '';
$nombre = '';
$madreEncargada = '';
$familia = '';
$piso = '';
$grupo = '';

// 2. DETECTAR MODO EDICIÓN (MÉTODO GET)
if (isset($_GET['editar_id'])) {
    $id_editar = filter_input(INPUT_GET, 'editar_id', FILTER_VALIDATE_INT);
    if ($id_editar) {
        $stmt_buscar = $conexion->prepare('SELECT * FROM alumnos WHERE id_Alumnos = ?');
        $stmt_buscar->bind_param('i', $id_editar);
        $stmt_buscar->execute();
        $resultado_buscar = $stmt_buscar->get_result();
        if ($alumno_data = $resultado_buscar->fetch_assoc()) {
            $modo_editar = true;
            $id_Alumnos = $alumno_data['id_Alumnos'];
            $nombre = $alumno_data['Nombre'];
            $madreEncargada = $alumno_data['Madre_encargada'];
            $familia = $alumno_data['Familia'];
            $piso = $alumno_data['Piso'];
            $grupo = $alumno_data['Grupo'];
        }
        $stmt_buscar->close();
    }
}

// 3. PROCESAR GUARDADO O ACTUALIZACIÓN (MÉTODO POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_alumno_post = filter_input(INPUT_POST, 'id_Alumnos', FILTER_VALIDATE_INT);
    $nombre = trim($_POST['Nombre'] ?? '');
    $madreEncargada = trim($_POST['Madre_encargada'] ?? '');
    $familia = trim($_POST['Familia'] ?? '');
    $piso = trim($_POST['Piso'] ?? '');
    $grupo = trim($_POST['Grupo'] ?? '');

    if ($nombre === '') {
        $_SESSION['mensaje'] = 'El nombre del alumno es obligatorio.';
        $_SESSION['tipoMensaje'] = 'error';
    } else {
        if (!empty($id_alumno_post)) {
            // ACTUALIZAR REGISTRO EXISTENTE (Tu columna exacta: id_Alumnos)
            $stmt = $conexion->prepare('UPDATE alumnos SET Nombre=?, Madre_encargada=?, Familia=?, Piso=?, Grupo=? WHERE id_Alumnos=?');
            $stmt->bind_param('sssssi', $nombre, $madreEncargada, $familia, $piso, $grupo, $id_alumno_post);
            $texto_exito = 'Datos del alumno actualizados correctamente.';
        } else {
            // INSERTAR NUEVO REGISTRO
            $stmt = $conexion->prepare('INSERT INTO alumnos (Nombre, Madre_encargada, Familia, Piso, Grupo) VALUES (?, ?, ?, ?, ?)');
            $stmt->bind_param('sssss', $nombre, $madreEncargada, $familia, $piso, $grupo);
            $texto_exito = 'Nuevo alumno registrado con éxito.';
        }
        
        if ($stmt && $stmt->execute()) {
            $_SESSION['mensaje'] = $texto_exito;
            $_SESSION['tipoMensaje'] = 'exito';
            $stmt->close();
        } else {
            $_SESSION['mensaje'] = 'Error al guardar en la Base de Datos: ' . $conexion->error;
            $_SESSION['tipoMensaje'] = 'error';
        }
    }
    header("Location: ver_alumnos.php"); // Te saca de aquí para proteger contra el F5
    exit();
}

// 4. PROCESAR ACCIÓN ELIMINAR (MÉTODO GET)
if (isset($_GET['eliminar_id'])) {
    $id_a_borrar = filter_input(INPUT_GET, 'eliminar_id', FILTER_VALIDATE_INT);
    if ($id_a_borrar) {
        $stmt_borrar = $conexion->prepare('DELETE FROM alumnos WHERE id_Alumnos = ?');
        if ($stmt_borrar) {
            $stmt_borrar->bind_param('i', $id_a_borrar);
            if ($stmt_borrar->execute()) {
                $_SESSION['mensaje'] = 'Registro de alumno eliminado correctamente.';
                $_SESSION['tipoMensaje'] = 'exito';
            } else {
                $_SESSION['mensaje'] = 'No se pudo eliminar el registro.';
                $_SESSION['tipoMensaje'] = 'error';
            }
            $stmt_borrar->close();
        }
    }
    header("Location: ver_alumnos.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Registro de Alumnos</title>
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
      <div class="alerta <?php echo $tipoMensaje; ?>" style="padding: 10px; margin: 15px auto; max-width: 700px; text-align: center; border-radius: 5px; font-weight: bold; background-color: <?php echo $tipoMensaje === 'exito' ? '#d4edda' : '#f8d7da'; ?>; color: <?php echo $tipoMensaje === 'exito' ? '#155724' : '#721c24'; ?>;">
          <?php echo $mensaje; ?>
      </div>
  <?php endif; ?>

  <div class="contenedor-formulario" style="max-width: 700px;">
      <h2><?php echo $modo_editar ? '📝 Modificar Alumno' : 'Registro de Alumnos'; ?></h2>
      
      <form method="post" action="alumnos.php">
        <input type="hidden" name="id_Alumnos" value="<?php echo $id_Alumnos; ?>">

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
            <div class="grupo-campo" style="grid-column: 1 / -1;">
                <label>Nombre del Alumno</label>
                <input type="text" name="Nombre" value="<?php echo htmlspecialchars($nombre); ?>" maxlength="100" required>
            </div>
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

        <button type="submit" class="boton-guardar" style="margin-top: 20px; width: 100%;">
            <?php echo $modo_editar ? 'Actualizar Datos del Alumno' : 'Guardar alumno'; ?>
        </button>
        
        <?php if ($modo_editar): ?>
            <a href="alumnos.php" style="display: block; text-align: center; margin-top: 10px; color: #721c24; text-decoration: none; font-weight: bold;">❌ Cancelar Edición</a>
        <?php endif; ?>
      </form>
      
      <!-- Enlace directo agregado para ir a la nueva página de la tabla -->
      <a href="ver_alumnos.php" style="display: block; text-align: center; margin-top: 20px; font-weight: bold; color: white; text-decoration: none; background-color: #28a745; padding: 10px; border-radius: 5px;">🔍 Ver Todos los Alumnos Registrados</a>
  </div>

  <div class="z1">
        <a class="z2" href="registro.html">REGRESAR</a>
  </div>
</body>
</html>