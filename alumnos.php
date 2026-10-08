<?php
$servidor = "localhost";
$usuario  = "root";
$password = "";
$base_datos = "biblioteca-bd";

$conexion = new mysqli($servidor, $usuario, $password, $base_datos);

if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

// 2. CONSULTA A LA BASE DE DATOS
$sql = "SELECT id_Alumnos, Nombre, Madre_encargada, Familia, Piso, Grupo FROM alumnos";
$resultado = $conexion->query($sql);
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
  </style>
</head>
<body>

  <h2 style="text-align: center;">Préstamos a Alumnos</h2>
  <div class="z1">
        <a class="z2" href="registro.html">REGRESAR</a>
  </div>
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
              echo "<td>" . $fila['id_Alumnos'] . "</td>";
              echo "<td>" . $fila['Nombre'] . "</td>";
              echo "<td>" . $fila['Madre_encargada'] . "</td>";
              echo "<td>" . $fila['Familia'] . "</td>";
              echo "<td>" . $fila['Piso'] . "</td>";
              echo "<td>" . $fila['Grupo'] . "</td>";
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