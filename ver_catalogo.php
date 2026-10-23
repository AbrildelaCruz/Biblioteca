<?php
require_once 'conexion.php';

// 1. RECUPERAR MENSAJES DE LA SESIÓN TRAS BORRAR O EDITAR
$mensaje = $_SESSION['mensaje'] ?? '';
$tipoMensaje = $_SESSION['tipoMensaje'] ?? '';
unset($_SESSION['mensaje'], $_SESSION['tipoMensaje']);

// 2. CONSULTAR LOS DATOS ACTUALIZADOS DEL CATÁLOGO
$resultado = $conexion->query('SELECT * FROM libros ORDER BY id_libro DESC');
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Catálogo Maestro - Libros Registrados</title>
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
  </div>

  <?php if ($mensaje !== ''): ?>
      <div class="alerta <?php echo $tipoMensaje; ?>" style="padding: 10px; margin: 15px auto; max-width: 850px; text-align: center; border-radius: 5px; font-weight: bold; background-color: <?php echo $tipoMensaje === 'exito' ? '#d4edda' : '#f8d7da'; ?>; color: <?php echo $tipoMensaje === 'exito' ? '#155724' : '#721c24'; ?>;">
          <?php echo $mensaje; ?>
      </div>
  <?php endif; ?>

  <h2 style="text-align: center; color: white; margin-top: 30px;">📚 Libros en el Catálogo Maestro</h2>

  <table class="tabla-datos" style="max-width: 98%; margin: 30px auto; font-size: 13px;">
      <thead>
          <tr>
              <th>ID</th>
              <th>Título del Libro</th>
              <th>Autor</th>
              <th>Editorial</th>
              <th>ISBM</th>
              <th>Año</th>
              <th>Clasificación</th>
              <th>Estado de Conservación</th>
              <th>Ej. Totales</th>
              <th>Ej. Disponibles</th>
              <th>Ej. Prestados</th>
              <th>Acciones</th>
          </tr>
      </thead>
      <tbody>
          <?php if ($resultado && $resultado->num_rows > 0): 
              while ($fila = $resultado->fetch_assoc()): ?>
              <tr>
                  <td><?php echo $fila['id_libro']; ?></td>
                  <td><b><?php echo htmlspecialchars($fila['Titulo']); ?></b></td>
                  <td><?php echo htmlspecialchars($fila['Autor']); ?></td>
                  <td><?php echo htmlspecialchars($fila['Editorial']); ?></td>
                  <td><?php echo htmlspecialchars($fila['ISBM']); ?></td>
                  <td><?php echo htmlspecialchars($fila['ano'] ?? '-'); ?></td>
                  <td><?php echo htmlspecialchars($fila['clasificacion'] ?? '-'); ?></td>
                  <td><span style="padding: 2px 5px; background: #e9ecef; border-radius:3px; font-weight:500;"><?php echo htmlspecialchars($fila['Estado_conservación'] ?? 'Excelente'); ?></span></td>
                  <td style="text-align: center;"><?php echo $fila['Ejemplares_totales']; ?></td>
                  <td style="text-align: center; color: green; font-weight: bold;"><?php echo $fila['Ejemplares_disponibles']; ?></td>
                  <td style="text-align: center; color: red;"><?php echo $fila['Ejemplares_prestados']; ?></td>
                  <td>
                      <a href="catalogo.php?editar_id=<?php echo $fila['id_libro']; ?>" style="text-decoration: none; padding: 3px 6px; background-color: #e2e3e5; border-radius: 3px; color: #383d41; font-size: 11px;">Editar</a>
                      <a href="catalogo.php?eliminar_id=<?php echo $fila['id_libro']; ?>" style="text-decoration: none; padding: 3px 6px; background-color: #f8d7da; border-radius: 3px; color: #721c24; font-size: 11px; margin-left: 2px;" onclick="return confirm('¿Seguro que deseas eliminar este libro?');">Eliminar</a>
                  </td>
              </tr>
          <?php endwhile; 
          else: ?>
              <tr>
                  <td colspan="12" style="text-align: center;">No hay libros registrados en el catálogo.</td>
              </tr>
          <?php endif; ?>
      </tbody>
  </table>

  <div style="text-align: center; margin-bottom: 40px;">
      <a href="catalogo.php" style="display: inline-block; padding: 10px 20px; background-color: #007bff; color: white; text-decoration: none; border-radius: 5px; font-weight: bold;">➕ Registrar Nuevo Libro</a>
  </div>

  <div class="z1">
      <a class="z2" href="registro.html">REGRESAR</a>
  </div>

</body>
</html>