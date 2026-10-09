<?php
$servidor = "localhost";
$usuario  = "root";
$password = "";
$base_datos = "biblioteca-bd";

$conexion = new mysqli($servidor, $usuario, $password, $base_datos);

if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}
$conexion->set_charset("utf8mb4");
?>

/* conexion alumno*/
<?php
require_once __DIR__ . '/conexion.php';

$mensaje = '';
$tipoMensaje = '';

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
      
      // Cerrar la conexión por seguridad
      $conexion->close();
      ?>
?>
/*conexion-profesores.php*/