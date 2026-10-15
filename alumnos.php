 <?php
include 'conexion.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Préstamos alumnos</title>
  <link rel="stylesheet" href="style.css?v=<?php echo time(); ?>">

</head>
<body>

  <h2 style="text-align: center;">Registro de Alumnos</h2>
  <div class="z1">
        <a class="z2" href="registro.html">REGRESAR</a>
  </div>
  
  <form class="formulario" method="post" action="alumnos.php">
    <label>Nombre<input type="text" name="Nombre" maxlength="100" required></label>
    <label>Madre encargada<input type="text" name="Madre_encargada" maxlength="100"></label>
    <label>Familia<input type="text" name="Familia" maxlength="100"></label>
    <label>Piso<input type="text" name="Piso" maxlength="20"></label>
    <label>Grupo<input type="text" name="Grupo" maxlength="20"></label>
    <button type="submit">Guardar alumno</button>
  </form>
  <table class="tabla-datos">
    <thead>
      <tr>
        <th>id_Alumnos</th>
        <th>Nombre</th>
        <th>Madre_encargada</th>
        <th>Familia</th>
        <th>Piso</th>
        <th>Grupo</th>
      </tr>
    </thead>
    <tbody>
      <?php
      $resultado = $conexion->query(
  'SELECT id_Alumnos, Nombre, Madre_encargada, Familia, Piso, Grupo FROM alumnos ORDER BY id_Alumnos DESC'
);

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
          }
      } else {
          echo "<tr><td colspan='6' style='text-align:center;'>No hay registros en la base de datos</td></tr>";
      }
      ?>
    </tbody>
  </table>
  <?php
//alumnos

$mensaje = '';
$tipoMensaje = '';
if ($mensaje !== '') {
    echo '<p class="mensaje ' . htmlspecialchars($tipoMensaje, ENT_QUOTES, 'UTF-8') . '">';
    echo htmlspecialchars($mensaje, ENT_QUOTES, 'UTF-8');
    echo '</p>';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $nombre = trim($_POST['Nombre'] ?? '');
  $madreEncargada = trim($_POST['Madre_encargada'] ?? '');
  $familia = trim($_POST['Familia'] ?? '');
  $piso = trim($_POST['Piso'] ?? '');
  $grupo = trim($_POST['Grupo'] ?? '');

  if ($nombre === '') {
    $mensaje = 'El nombre del alumno es obligatorio.';
    $tipoMensaje = 'error';
  } else {
    $stmt = $conexion->prepare(
      'INSERT INTO alumnos (Nombre, Madre_encargada, Familia, Piso, Grupo) VALUES (?, ?, ?, ?, ?)'
    );
    $stmt->bind_param('sssss', $nombre, $madreEncargada, $familia, $piso, $grupo);

    if ($stmt->execute()) {
      $mensaje = 'Alumno registrado correctamente.';
      $tipoMensaje = 'exito';
    } else {
      $mensaje = 'No se pudo guardar el alumno: ' . $stmt->error;
      $tipoMensaje = 'error';
    }
    $stmt->close();
  }
}

?>

</body>
</html>