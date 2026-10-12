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

         <form class="formulario" method="post" action="inventario.php">
          <label>id_inventario<input type="text" name="id_inventario" maxlength="20" required></label>
          <label>id_libro<input type="text" name="id_libro" maxlength="20" required></label>
          <label>Estado_conservación<input type="text"  name="estado_conservacion" maxlength="50" required></label>
          <button type="submit">Guardar</button>
         </form>

    <table class="tab">
        <thead>
    <tr>
      <th>id_inventario</th>
      <th>id_libro</th>
      <th>Estado_conservación</th>
    </tr>
  </thead>
  <!-- Cuerpo -->
  <tbody>
    <tr>
      <td>1</td>
      <td>01</td>
      <td>Disponible</td>
    </tr>
    <tr>
      <td>2</td>
      <td>02</td>
      <td>Disponible</td>
    </tr> 
    <tr>
      <td>3</td>
      <td>03</td>
      <td>Disponible</td>
    </tr>
  </tbody>
    </table>
</body>  


