<?php
include 'conexion.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css?v=<?php echo time(); ?>">
    <title>Inventario</title>
</head>
<body>
   
    <h1>Inventario</h1>
     <div class="q1">
      <p style="text-align:center"><a class="q2" href="registro.html">REGRESAR</a></p>
      </div>
      <br>
         <br>
         <?php
         if ($mensaje !== ''): ?>
        <p class="mensaje <?= htmlspecialchars($tipoMensaje, ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($mensaje, ENT_QUOTES, 'UTF-8') ?></p>
         <?php endif; ?>
    <table class="tab">
        <thead>
    <tr>
      <th>id_inventario</th>
      <th>id_libro</th>
      <th>Estado_Conservación</th>
    </tr>
  </thead>
  <!-- Cuerpo -->
  <tbody>
    <tr>
      <td>01</td>
      <td></td>
      <td></td>
    </tr>
    <tr>
      <td>02</td>
      <td></td>
      <td></td>
    </tr> 
    <tr>
      <td>03</td>
      <td></td>
      <td></td>
    </tr>
  </tbody>
    </table>
</body>
</html>