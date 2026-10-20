<?php
require_once 'conexion.php';

// 1. RECUPERAR MENSAJES DE LA SESIÓN
session_start();
$mensaje = $_SESSION['mensaje'] ?? '';
$tipoMensaje = $_SESSION['tipoMensaje'] ?? '';
unset($_SESSION['mensaje'], $_SESSION['tipoMensaje']);

// Variables para el modo edición
$modo_editar = false;
$id_Alumnos = '';
$nombre = '';
$madreEncargada = '';
$familia = '';
$piso = '';
$grupo = '';

// 2. DETECTAR SI SE SOLICITA EDITAR UN REGISTRO (MÉTODO GET)
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

// 3. PROCESAR EL FORMULARIO (REGISTRO O ACTUALIZACIÓN - MÉTODO POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $id_alumno_post = $_POST['id_Alumnos'] ?? '';
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
        // ACCIÓN: ACTUALIZAR REGISTRO EXISTENTE
        $stmt = $conexion->prepare('UPDATE alumnos SET Nombre=?, Madre_encargada=?, Familia=?, Piso=?, Grupo=? WHERE id_Alumnos=?');
        $stmt->bind_param('sssssi', $nombre, $madreEncargada, $familia, $piso, $grupo, $id_alumno_post);
        $texto_exito = 'Alumno actualizado correctamente.';
    } else {
        // ACCIÓN: INSERTAR NUEVO REGISTRO
        $stmt = $conexion->prepare('INSERT INTO alumnos (Nombre, Madre_encargada, Familia, Piso, Grupo) VALUES (?, ?, ?, ?, ?)');
        $stmt->bind_param('sssss', $nombre, $madreEncargada, $familia, $piso, $grupo);
        $texto_exito = 'Alumno registrado correctamente.';
    }
    
    if (!$stmt) {
        $_SESSION['mensaje'] = 'Error en el sistema: ' . $conexion->error;
        $_SESSION['tipoMensaje'] = 'error';
    } else {
        if ($stmt->execute()) {
          $_SESSION['mensaje'] = $texto_exito;
          $_SESSION['tipoMensaje'] = 'exito';
        } else {
          $_SESSION['mensaje'] = 'No se guardaron los cambios: ' . $stmt->error;
          $_SESSION['tipoMensaje'] = 'error';
        }
        $stmt->close();
    }
  }
  header("Location: alumnos.php");
  exit();
}

// 4. PROCESAR EL BORRADO REAL EN LA BASE DE DATOS (MÉTODO GET)
if (isset($_GET['eliminar_id'])) {
    $id_a_borrar = filter_input(INPUT_GET, 'eliminar_id', FILTER_VALIDATE_INT);
    if ($id_a_borrar) {
        $stmt_borrar = $conexion->prepare('DELETE FROM alumnos WHERE id_Alumnos = ?');
        if ($stmt_borrar) {
            $stmt_borrar->bind_param('i', $id_a_borrar);
            if ($stmt_borrar->execute()) {
                $_SESSION['mensaje'] = 'Alumno eliminado correctamente.';
                $_SESSION['tipoMensaje'] = 'exito';
            } else {
                $_SESSION['mensaje'] = 'Error al eliminar.';
                $_SESSION['tipoMensaje'] = 'error';
            }
            $stmt_borrar->close();
        }
    }
    header("Location: alumnos.php");
    exit();
}

// 5. CONSULTAR LOS DATOS ACTUALIZADOS
$resultado = $conexion->query('SELECT id_Alumnos, Nombre, Madre_encargada, Familia, Piso, Grupo FROM alumnos ORDER BY id_Alumnos DESC');
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Préstamos alumnos</title>
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
      <div class="burbuja"></div>
      <div class="burbuja"></div>
      <div class="burbuja"></div>
  </div>

  <?php if ($mensaje !== ''): ?>
      <div style="padding: 10px; margin: 15px auto; max-width: 700px; text-align: center; border-radius: 5px; font-weight: bold; background-color: <?php echo $tipoMensaje === 'exito' ? '#d4edda' : '#f8d7da'; ?>; color: <?php echo $tipoMensaje === 'exito' ? '#155724' : '#721c24'; ?>; z-index: 10; position: relative;">
          <?php echo $mensaje; ?>
      </div>
  <?php endif; ?>

  <div class="contenedor-formulario">
      <div class="contenedor-formulario">
      <h2><?php echo $modo_editar ? '📝 Modificar Alumno' : 'Registro de Alumnos'; ?></h2>
      
      <form method="post" action="alumnos.php">
        <!-- Campo oculto para rastrear el ID al actualizar -->
        <input type="hidden" name="id_Alumnos" value="<?php echo $id_Alumnos; ?>">

        <div class="grupo-campo">
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

        <button type="submit" class="boton-guardar">
            <?php echo $modo_editar ? 'Actualizar Datos' : 'Guardar alumno'; ?>
        </button>
        
        <?php if ($modo_editar): ?>
            <a href="alumnos.php" style="display: block; text-align: center; margin-top: 10px; color: #721c24; text-decoration: none; font-weight: bold;">❌ Cancelar Edición</a>
        <?php endif; ?>
      </form>
  </div>
  </div>

  <table class="tabla-datos">
    <thead>
      <tr>
        <th>ID</th>
        <th>Nombre</th>
        <th>Madre Encargada</th>
        <th>Familia</th>
        <th>Piso</th>
        <th>Grupo</th>
        <th>Acción</th>
      </tr>
    </thead>
    <tbody>
      <?php
    if ($resultado && $resultado->num_rows > 0) {
        while($fila = $resultado->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . (int) $fila['id_Alumnos'] . "</td>";
            echo "<td>" . htmlspecialchars($fila['Nombre'], ENT_QUOTES, 'UTF-8') . "</td>";
            echo "<td>" . htmlspecialchars($fila['Madre_encargada'] ?? '', ENT_QUOTES, 'UTF-8') . "</td>";
            echo "<td>" . htmlspecialchars($fila['Familia'] ?? '', ENT_QUOTES, 'UTF-8') . "</td>";
            echo "<td>" . htmlspecialchars($fila['Piso'] ?? '', ENT_QUOTES, 'UTF-8') . "</td>";
            echo "<td>" . htmlspecialchars($fila['Grupo'] ?? '', ENT_QUOTES, 'UTF-8') . "</td>";
            
            // Columna de Acciones combinada: Editar y Eliminar de forma permanente
            echo "<td>";
            echo '<a href="alumnos.php?editar_id=' . $fila['Acción'] . '" style="text-decoration: none; display: inline-block; padding: 5px 10px; background-color: #007bff; border: 1px solid #007bff; border-radius: 3px; color: white; font-size: 13px; font-weight: bold; margin-right: 5px;">Editar</a>';
            echo '<a href="alumnos.php?eliminar_id=' . $fila['Acción'] . '" style="text-decoration: none; display: inline-block; padding: 5px 10px; background-color: #fff; border: 1px solid #ccc; border-radius: 3px; color: black; font-size: 13px;" onclick="return confirm(\'¿Seguro que deseas eliminar este alumno permanentemente?\');">Eliminar</a>';
            echo "</td>";
            
            echo "</tr>";
        }
    } else {
        echo "<tr><td colspan='7' style='text-align:center;'>No hay alumnos registrados actualmente.</td></tr>";
    }
    ?>
    </tbody>
  </table>
  <div class="z1">
        <a class="z2" href="registro.html">REGRESAR</a>
  </div>
  <script>
    const boton = document.getElementById('btn-modificar');
const texto = document.getElementById('grupo-campo');

boton.addEventListener('click',() => {
    if(boton.textContent.includes('Modificar')){
        const contenidoActual = Text.textContent;
        texto.inneHTML= '<iput type ="text" id="inputEditando" value="${contenidoActual}" style="font-size:16px; padding:5px">';
        boton.innerHTML = 'Guardar';
        boton.style.backgroundColor ='#000';
    } else{
        const nuevoTexto = document.getElementById('inputEditando').value;
        texto.textContent = nuevaTexto;
        boton.innerHTML='Modificar'; 
        boton.style.backgroundColor = '#2563eb'
    }
});
  </script>
</body>
</html>