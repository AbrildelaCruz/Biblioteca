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
     <style>
.tab th{
  background-color:#1849B7;
  color: #143D99;
  padding: 12px 15px;
  text-align: left;
  font-weight: bold;
}

.tab td{
  padding: 12px 15px;
  border-bottom: 1px solid white;
  color: #555;
}

.tab tr:nth-child(even) {
  background-color: #f9f9f9;
}

.tab tbody tr:hover {
  background-color: #f1f1f1;
}
        </style>

    <h1>Inventario</h1>
     <div class="q1">
        <a class="q2" href="registro.html">REGRESAR</a>
      </div>
      <br>
         <br>
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