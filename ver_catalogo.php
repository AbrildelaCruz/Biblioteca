<?php
require_once 'conexion.php';

// Iniciar sesión si no está iniciada para evitar errores con las alertas de sesión
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Recuperar alertas si el usuario eliminó o editó algo
$mensaje = $_SESSION['mensaje'] ?? '';
$tipoMensaje = $_SESSION['tipoMensaje'] ?? '';
unset($_SESSION['mensaje'], $_SESSION['tipoMensaje']);

// ==========================================
//        CONFIGURACIÓN DEL PAGINADOR
// ==========================================
$registros_por_pagina = 10; // Cantidad de libros que deseas por página

// Obtener la página actual desde la URL, si no existe por defecto es la página 1
$pagina_actual = isset($_GET['pagina']) ? filter_input(INPUT_GET, 'pagina', FILTER_VALIDATE_INT) : 1;
if (!$pagina_actual || $pagina_actual < 1) {
    $pagina_actual = 1;
}

// Calcular desde qué registro de la base de datos se debe empezar a leer (OFFSET)
$desplazamiento = ($pagina_actual - 1) * $registros_por_pagina;

// Contar el total de libros existentes para calcular las páginas necesarias
$sql_total = "SELECT COUNT(*) AS total FROM libros";
$resultado_total = $conexion->query($sql_total);
$fila_total = $resultado_total->fetch_assoc();
$total_registros = $fila_total['total'];

// Calcular el número total de páginas (redondeando hacia arriba)
$total_paginas = ceil($total_registros / $registros_por_pagina);

// Consultar los libros limitados correspondientes a la página activa
$sql_datos = "SELECT * FROM libros ORDER BY id_libro ASC LIMIT ? OFFSET ?";
$stmt = $conexion->prepare($sql_datos);
$stmt->bind_param("ii", $registros_por_pagina, $desplazamiento);
$stmt->execute();
$resultado = $stmt->get_result();
// ==========================================
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Catálogo Maestro - Base de Datos</title>
  <link rel="stylesheet" href="style.css?v=<?php echo time(); ?>">
  <link rel="icon" type="image/png" href="logobiblioteca.png">
  
  <style>
      /* Estilos profesionales para la botonera del paginador */
      .paginador {
          display: flex;
          justify-content: center;
          gap: 6px;
          margin-top: 20px;
          margin-bottom: 30px;
      }
      .paginador a {
          color: #4a148c;
          background-color: white;
          border: 1px solid #e1bee7;
          padding: 8px 14px;
          text-decoration: none;
          border-radius: 4px;
          font-weight: bold;
          font-size: 13px;
          transition: all 0.2s ease;
      }
      .paginador a:hover {
          background-color: #f3e5f5;
          border-color: #4a148c;
      }
      .paginador a.activa {
          background-color: #4a148c;
          color: white;
          border-color: #4a148c;
          cursor: default;
      }
      .paginador a.deshabilitado {
          color: #ccc;
          border-color: #eee;
          cursor: not-allowed;
          pointer-events: none;
      }
  </style>
</head>
<body class="body2">

  <div class="contenedor-burbujas">
      <div class="burbuja"></div>
      <div class="burbuja"></div>
      <div class="burbuja"></div>
  </div>

  <?php if ($mensaje !== ''): ?>
      <div class="alerta <?php echo $tipoMensaje; ?>" style="padding: 10px; margin: 15px auto; max-width: 850px; text-align: center; border-radius: 5px; font-weight: bold; background-color: <?php echo $tipoMensaje === 'exito' ? '#d4edda' : '#f8d7da'; ?>; color: <?php echo $tipoMensaje === 'exito' ? '#155724' : '#721c24'; ?>;">
          <?php echo $mensaje; ?>
      </div>
  <?php endif; ?>

  <h2 style="text-align: center; color: white; margin-top: 30px;">📚 Libros Registrados en la Base de Datos</h2>

  <table class="tabla-datos" style="max-width: 95%; margin: 30px auto; font-size: 13px;">
      <thead>
          <tr>
              <th>N°</th> <!-- Reemplazado ID por N° Consecutivo -->
              <th>Título-libro</th>
              <th>Autor</th>
              <th>Editorial</th>
              <th>ISBN</th>
              <th>Año</th>
              <th>Clasificación</th>
              <th>Estado de conservación</th>
              <th>Ej. Totales</th>
              <th>EJ. Disponibles</th>
              <th>Ej. Prestados</th>
              <th>Acciones</th>
          </tr>
      </thead>
      <tbody>
          <?php if ($resultado && $resultado->num_rows > 0): 
              // Inicializar contador según el desplazamiento de la página actual
              $num = $desplazamiento + 1;
              
              while ($fila = $resultado->fetch_assoc()): ?>
              <tr>
                  <!-- Mostramos el número dinámico consecutivo -->
                  <td style="font-weight: bold; text-align: center;"><?php echo $num; ?></td>
                  <td><b><?php echo htmlspecialchars($fila['Titulo']); ?></b></td>
                  <td><?php echo htmlspecialchars($fila['Autor']); ?></td>
                  <td><?php echo htmlspecialchars($fila['Editorial']); ?></td>
                  <td><?php echo htmlspecialchars($fila['ISBN']); ?></td>
                  <td><?php echo htmlspecialchars($fila['ano'] ?? '-'); ?></td>
                  <td><?php echo htmlspecialchars($fila['clasificacion'] ?? '-'); ?></td>
                  <td><span style="padding: 2px 5px; background: #e9ecef; border-radius:3px; font-weight:500;"><?php echo htmlspecialchars($fila['Estado_conservación'] ?? 'Excelente'); ?></span></td>
                  <td style="text-align: center;"><?php echo $fila['Ejemplares_totales']; ?></td>
                  <td style="text-align: center; color: green; font-weight: bold;"><?php echo $fila['Ejemplares_disponibles']; ?></td>
                  <td style="text-align: center; color: red;"><?php echo $fila['Ejemplares_prestados']; ?></td>
                  <td>
                      <!-- Las acciones conservan internamente la llave primaria id_libro para interactuar con la base de datos -->
                      <a href="catalogo.php?editar_id=<?php echo $fila['id_libro']; ?>" style="text-decoration: none; padding: 3px 6px; background-color: #e2e3e5; border-radius: 3px; color: #383d41; font-size: 11px;">Editar</a>
                      <a href="catalogo.php?eliminar_id=<?php echo $fila['id_libro']; ?>" style="text-decoration: none; padding: 3px 6px; background-color: #f8d7da; border-radius: 3px; color: #721c24; font-size: 11px; margin-left: 2px;" onclick="return confirm('¿Seguro que deseas eliminar este libro?');">Eliminar</a>
                  </td>
              </tr>
          <?php 
              $num++; // Incrementar número consecutivo
              endwhile; 
          else: ?>
              <tr>
                  <td colspan="12" style="text-align: center;">No hay libros registrados en el catálogo.</td>
              </tr>
          <?php endif; ?>
      </tbody>
  </table>

  <!-- ==========================================
        RENDERIZADO VISUAL DEL PAGINADOR
  ========================================== -->
  <?php if ($total_paginas > 1): ?>
  <div class="paginador">
      <!-- Botón Anterior -->
      <a href="?pagina=<?php echo $pagina_actual - 1; ?>" class="<?php echo ($pagina_actual <= 1) ? 'deshabilitado' : ''; ?>">« Anterior</a>

      <!-- Lista de números de página -->
      <?php for ($i = 1; $i <= $total_paginas; $i++): ?>
          <a href="?pagina=<?php echo $i; ?>" class="<?php echo ($pagina_actual == $i) ? 'activa' : ''; ?>">
              <?php echo $i; ?>
          </a>
      <?php endfor; ?>

      <!-- Botón Siguiente -->
      <a href="?pagina=<?php echo $pagina_actual + 1; ?>" class="<?php echo ($pagina_actual >= $total_paginas) ? 'deshabilitado' : ''; ?>">Siguiente »</a>
  </div>
  <?php endif; ?>
  <!-- ========================================== -->

  <div style="text-align: center; margin-bottom: 40px;">
      <a href="catalogo.php" style="display: inline-block; padding: 10px 20px; background-color: #007bff; color: white; text-decoration: none; border-radius: 5px; font-weight: bold;">➕ Registrar Nuevo Libro</a>
  </div>

  <div class="z1">
      <a class="z2" href="registro.html">REGRESAR</a>
  </div>

</body>
</html>