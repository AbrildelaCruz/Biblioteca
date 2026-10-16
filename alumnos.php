 <?php
require_once 'conexion.php';

$mensaje = '';
$tipoMensaje = '';

// 1. PROCESAR EL FORMULARIO PRIMERO (Para atrapar el registro antes de consultar la tabla)
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
    
    if (!$stmt) {
        $mensaje = 'Error al preparar el registro: ' . $conexion->error;
        $tipoMensaje = 'error';
    } else {
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
}

// 2. CONSULTAR LOS DATOS ACTUALIZADOS
$resultado = $conexion->query(
  'SELECT id_Alumnos, Nombre, Madre_encargada, Familia, Piso, Grupo FROM alumnos ORDER BY id_Alumnos DESC'
);
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Préstamos alumnos</title>
  <link rel="stylesheet" href="style.css?v=<?php echo time(); ?>">
</head>
<body class="body2"> <!-- Hereda tu fondo animado -->

  <!-- Animación de burbujas de fondo -->
  <div class="contenedor-burbujas">
      <div class="burbuja"></div>
      <div class="burbuja"></div>
      <div class="burbuja"></div>
      <div class="burbuja"></div>
      <div class="burbuja"></div>
      <div class="burbuja"></div>
  </div>

  <!-- Alerta de mensajes del sistema -->
  <?php if ($mensaje !== ''): ?>
      <div style="padding: 10px; margin: 15px auto; max-width: 700px; text-align: center; border-radius: 5px; font-weight: bold; background-color: <?php echo $tipoMensaje === 'exito' ? '#d4edda' : '#f8d7da'; ?>; color: <?php echo $tipoMensaje === 'exito' ? '#155724' : '#721c24'; ?>; z-index: 10; position: relative;">
          <?php echo $mensaje; ?>
      </div>
  <?php endif; ?>

  <!-- Contenedor adaptado a tu estilo de cuadrícula -->
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

  <!-- Tabla de datos adaptada a tus estilos claros -->
  <table class="tabla-datos">
    <thead>
      <tr>
        <th>ID</th>
        <th>Nombre</th>
        <th>Madre Encargada</th>
        <th>Familia</th>
        <th>Piso</th>
        <th>Grupo</th>
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
          }
      } else {
          echo "<tr><td colspan='6' style='text-align:center;'>No hay alumnos registrados actualmente.</td></tr>";
      }
      ?>
    </tbody>
  </table>

  <!-- Botón regresar fijo -->
  <div class="z1">
        <a class="z2" href="registro.html">REGRESAR</a>
  </div>

</body>
</html>