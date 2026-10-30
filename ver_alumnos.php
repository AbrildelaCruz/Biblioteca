<?php
require_once 'conexion.php';

$mensaje = $_SESSION['mensaje'] ?? '';
$tipoMensaje = $_SESSION['tipoMensaje'] ?? '';
unset($_SESSION['mensaje'], $_SESSION['tipoMensaje']);

// Consulta usando tus nombres exactos de columnas
$resultado = $conexion->query('SELECT id_Alumnos, Nombre, Madre_encargada, Familia, Piso, Grupo FROM alumnos ORDER BY id_Alumnos DESC');
?>
<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Lista de Alumnos - Panel</title>
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
    <div class="alerta <?php echo $tipoMensaje; ?>"
      style="padding: 10px; margin: 15px auto; max-width: 850px; text-align: center; border-radius: 5px; font-weight: bold; background-color: <?php echo $tipoMensaje === 'exito' ? '#d4edda' : '#f8d7da'; ?>; color: <?php echo $tipoMensaje === 'exito' ? '#155724' : '#721c24'; ?>;">
      <?php echo $mensaje; ?>
    </div>
  <?php endif; ?>

  <h2 style="text-align: center; color: white; margin-top: 30px;">👥 Alumnos Guardados en el Sistema</h2>

  <table class="tabla-datos" style="max-width: 95%; margin: 30px auto; font-size: 13px;">
    <thead>
      <tr>
        <th>ID</th>
        <th>Nombre Completo</th>
        <th>Madre Encargada</th>
        <th>Familia</th>
        <th>Piso</th>
        <th>Grupo</th>
        <th>Acciones</th>
      </tr>
    </thead>
    <tbody>
      <?php
      if ($resultado && $resultado->num_rows > 0) {
        while ($fila = $resultado->fetch_assoc()) {
          $id_actual = (int) $fila['id_Alumnos'];
          echo "<tr>";
          echo "<td>" . $id_actual . "</td>";
          echo "<td><b>" . htmlspecialchars($fila['Nombre']) . "</b></td>";
          echo "<td>" . htmlspecialchars($fila['Madre_encargada'] ?? '-') . "</td>";
          echo "<td>" . htmlspecialchars($fila['Familia'] ?? '-') . "</td>";
          echo "<td style='text-align: center;'>" . htmlspecialchars($fila['Piso'] ?? '-') . "</td>";
          echo "<td style='text-align: center;'>" . htmlspecialchars($fila['Grupo'] ?? '-') . "</td>";
          echo "<td>";
          echo '<a href="alumnos.php?editar_id=' . $id_actual . '" style="text-decoration: none; padding: 3px 6px; background-color: #e2e3e5; border-radius: 3px; color: #383d41; font-size: 11px; margin-right: 5px;">Editar</a>';
          echo '<a href="alumnos.php?eliminar_id=' . $id_actual . '" style="text-decoration: none; padding: 3px 6px; background-color: #f8d7da; border-radius: 3px; color: #721c24; font-size: 11px;" onclick="return confirm(\'¿Seguro que deseas eliminar este alumno?\');">Eliminar</a>';
          echo "</td>";
          echo "</tr>";
        }
      } else {
        echo '<tr><td colspan="7" style="text-align: center;">No hay alumnos registrados actualmente.</td></tr>';
      }
      ?>
    </tbody>
  </table>

  <div style="text-align: center; margin-bottom: 40px;">
    <a href="alumnos.php"
      style="display: inline-block; padding: 10px 20px; background-color: #007bff; color: white; text-decoration: none; border-radius: 5px; font-weight: bold;">➕
      Registrar Nuevo Alumno</a>
  </div>

  <div class="z1">
    <a class="z2" href="registro.html">REGRESAR</a>
  </div>
</body>

</html>