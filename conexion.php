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
$sql ="";
$resultado = $conexion->query($sql);

    if ($resultado && $resultado->num_rows > 0) {
          while($fila = $resultado->fetch_assoc()) {
              echo "<tr>";
              echo "<td>" . $fila[''] . "</td>";
              echo "<td>" . $fila[''] . "</td>";
              echo "<td>" . $fila[''] . "</td>";
              echo "<td>" . $fila[''] . "</td>";
              echo "<td>" . $fila[''] . "</td>";
              echo "<td>" . $fila[''] . "</td>";
              echo "</tr>";
          }
      } else {
          echo "<tr><td colspan='6' style='text-align:center;'>No hay registros en la base de datos</td></tr>";
      }
      
      // Cerrar la conexión por seguridad
      $conexion->close();
      ?>