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
      // conexion profesores
<?php
require_once __DIR__ . '/conexion.php';

$mensaje = '';
$tipoMensaje = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['Nombre'] ?? '');
    $materia = trim($_POST['Materia'] ?? '');
    $libro = trim($_POST['Nombre del Libro'] ?? '');
    $cantidad = filter_input(INPUT_POST, 'Cantidad', FILTER_VALIDATE_INT);
    $fechaPrestamo = $_POST['Fecha_prestamo'] ?? '';
    $fechaEntrega = $_POST['Fecha_entrega'] ?? '';

    if ($nombre === '' || $libro === '' || $cantidad === false || $cantidad === null || $cantidad < 1 || $fechaPrestamo === '' || $fechaEntrega === '') {
        $mensaje = 'Completa todos los campos obligatorios. La cantidad debe ser mayor que cero.';
        $tipoMensaje = 'error';
    } elseif ($fechaEntrega < $fechaPrestamo) {
        $mensaje = 'La fecha de entrega no puede ser anterior a la fecha de préstamo.';
        $tipoMensaje = 'error';
    } else {
        $stmt = $conexion->prepare(
            'INSERT INTO profesores (Nombre, Materia, Nombre del Libro, Cantidad, Fecha_prestamo, Fecha_entrega) VALUES (?, ?, ?, ?, ?, ?)'
        );
        if (!$stmt) {
            $mensaje = 'No se pudo preparar el guardado: ' . $conexion->error;
            $tipoMensaje = 'error';
        } else {
            $stmt->bind_param('sssiss', $nombre, $materia, $libro, $cantidad, $fechaPrestamo, $fechaEntrega);
            if ($stmt->execute()) {
                $mensaje = 'Datos guardados en la tabla profesores.';
                $tipoMensaje = 'exito';
            } else {
                $mensaje = 'No se guardaron los datos en profesores: ' . $stmt->error;
                $tipoMensaje = 'error';
            }
            $stmt->close();
        }
    }
}

$resultado = $conexion->query(
    'SELECT id_Profesores, Nombre, Materia, Nombre del Libro, Cantidad, Fecha_prestamo, Fecha_entrega FROM profesores ORDER BY id_Profesores DESC'
);
?>

 <?php if ($resultado && $resultado->num_rows > 0): ?>
                <?php while ($fila = $resultado->fetch_assoc()): ?>
                    <tr>
                        <td><?= (int) $fila['id_Profesores'] ?></td>
                        <td><?= htmlspecialchars($fila['Nombre'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars($fila['Materia'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars($fila['Nombre del Libro'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= (int) $fila['Cantidad'] ?></td>
                        <td><?= htmlspecialchars($fila['Fecha_prestamo'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars($fila['Fecha_entrega'], ENT_QUOTES, 'UTF-8') ?></td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="7" style="text-align:center">No hay registros de profesores.</td></tr>
            <?php endif; ?>
              <?php $conexion->close(); ?>
