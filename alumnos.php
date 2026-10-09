<?php
include 'conexion.php';
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Préstamos alumnos</title>
  <style>
    .tabla-datos {
      width: 100%;
      max-width: 800px;
      margin: 20px auto;
      border-collapse: collapse;
      font-family: Arial, sans-serif;
    }
    .tabla-datos th, .tabla-datos td {
      border: 1px solid #ddd;
      padding: 12px;
      text-align: left;
    }
    .tabla-datos th {
      background-color: #2c3e50;
      color: white;
    }
    .tabla-datos tr:nth-child(even) { background-color: #f9f9f9; }
    .formulario { max-width: 800px; margin: 20px auto; display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 12px; }
    .formulario label { display: flex; flex-direction: column; gap: 5px; }
    .formulario button { padding: 10px; cursor: pointer; }
    .mensaje { max-width: 800px; margin: 16px auto; }
    .error { color: #b00020; }
    .exito { color: #16753b; }
  </style>
</head>
<body>

  <h2 style="text-align: center;">Registro de Alumnos</h2>
  <div class="z1">
        <a class="z2" href="registro.html">REGRESAR</a>
  </div>
  <?php if ($mensaje !== ''): ?>
    <p class="mensaje <?= htmlspecialchars($tipoMensaje, ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($mensaje, ENT_QUOTES, 'UTF-8') ?></p>
  <?php endif; ?>
  <form class="formulario" method="post" action="alumnos.php">
    <label>Nombre *<input type="text" name="Nombre" maxlength="100" required></label>
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
      
      // Cerrar la conexión por seguridad
      $conexion->close();
      ?>
    </tbody>
  </table>

</body>
</html>