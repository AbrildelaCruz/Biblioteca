<?php
require_once 'conexion.php';

// Recuperar mensajes de la sesión si existen
$mensaje = $_SESSION['mensaje'] ?? '';
$tipoMensaje = $_SESSION['tipoMensaje'] ?? '';

// Limpiar los mensajes de la sesión para que no se queden fijos
unset($_SESSION['mensaje'], $_SESSION['tipoMensaje']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $nombre = trim($_POST['Nombre'] ?? '');
  $madreEncargada = trim($_POST['Madre_encargada'] ?? '');
  $familia = trim($_POST['Familia'] ?? '');
  $piso = trim($_POST['Piso'] ?? '');
  $grupo = trim($_POST['Grupo'] ?? '');

  if ($nombre === '') {
    $_SESSION['mensaje'] = 'El nombre del alumno es obligatorio.';
    $_SESSION['tipoMensaje'] = 'error';
    header("Location: alumnos.php");
    exit();
  } else {
    $stmt = $conexion->prepare(
      'INSERT INTO alumnos (Nombre, Madre_encargada, Familia, Piso, Grupo) VALUES (?, ?, ?, ?, ?)'
    );
    
    if (!$stmt) {
        $_SESSION['mensaje'] = 'Error al preparar el registro: ' . $conexion->error;
        $_SESSION['tipoMensaje'] = 'error';
    } else {
        $stmt->bind_param('sssss', $nombre, $madreEncargada, $familia, $piso, $grupo);

        if ($stmt->execute()) {
          $_SESSION['mensaje'] = 'Alumno registrado correctamente.';
          $_SESSION['tipoMensaje'] = 'exito';
        } else {
          $_SESSION['mensaje'] = 'No se pudo guardar el alumno: ' . $stmt->error;
          $_SESSION['tipoMensaje'] = 'error';
        }
        $stmt->close();
    }
    // REDIRECCIÓN ESTRATÉGICA: Evita el reenvío de formulario al recargar con F5
    header("Location: alumnos.php");
    exit();
  }
}

// Consultar los datos actualizados
$resultado = $conexion->query(
  'SELECT id_Alumnos, Nombre, Madre_encargada, Familia, Piso, Grupo FROM alumnos ORDER BY id_Alumnos DESC'
);

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
                $_SESSION['mensaje'] = 'Error al eliminar en la base de datos.';
                $_SESSION['tipoMensaje'] = 'error';
            }
            $stmt_borrar->close();
        }
    }
    header("Location: alumnos.php");
    exit();
}
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
  </div>

  <?php if ($mensaje !== ''): ?>
      <div style="padding: 10px; margin: 15px auto; max-width: 700px; text-align: center; border-radius: 5px; font-weight: bold; background-color: <?php echo $tipoMensaje === 'exito' ? '#d4edda' : '#f8d7da'; ?>; color: <?php echo $tipoMensaje === 'exito' ? '#155724' : '#721c24'; ?>; z-index: 10; position: relative;">
          <?php echo $mensaje; ?>
      </div>
  <?php endif; ?>

  <div class="contenedor-formulario">
      <h2>Registro de Alumnos</h2>
      
      <form method="post" action="alumnos.php">
        <div class="grupo-campo">
            <label>Nombre del Alumno</label>
            <input type="text" name="Nombre" maxlength="100" required>
        </div>
        <div class="grupo-campo">
            <label>Madre Encargada</label>
            <input type="text" name="Madre_encargada" maxlength="100">
        </div>
        <div class="grupo-campo">
            <label>Familia</label>
            <input type="text" name="Familia" maxlength="100">
        </div>
        <div class="grupo-campo">
            <label>Piso</label>
            <input type="text" name="Piso" maxlength="20">
        </div>
        <div class="grupo-campo">
            <label>Grupo</label>
            <input type="text" name="Grupo" maxlength="20">
        </div>
        <button type="submit" class="boton-guardar">Guardar alumno</button>
      </form>
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
              echo "</tr>";

              echo "<td>";
              echo '<a href="alumnos.php?eliminar_id=' . $fila['Acción'] . '" style="text-decoration: none; display: inline-block; padding: 5px 10px; background-color: #fff; border: 1px solid #ccc; border-radius: 3px; color: black; font-size: 13px;" onclick="return confirm(\'¿Seguro que deseas eliminar este alumno permanentemente?\');">Eliminar</a>';
              echo "</td>";

              echo "</tr>";

               echo "<td>";
              echo '<a href="alumnos.php?Modificar_id=' . $fila['Acción'] . '" style="text-decoration: none; display: inline-block; padding: 5px 10px; background-color: #fff; border: 1px solid #ccc; border-radius: 3px; color: black; font-size: 13px;" onclick="return confirm(\'¿Seguro que deseas modificar este alumno ?\');">Modificar</a>';
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
  <script></
</body>
</html>