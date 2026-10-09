<?php
include 'conexion.php';
?>
| 
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Préstamos alumnos</title>
  <link rel="stylesheet" href="style.css?v=<?php echo time(); ?>">

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
     
    </tbody>
  </table>

</body>
</html>