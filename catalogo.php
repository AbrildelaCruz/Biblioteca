<?php
require_once 'conexion.php';

// 1. RECUPERAR MENSAJES DE LA SESIÓN
$mensaje = $_SESSION['mensaje'] ?? '';
$tipoMensaje = $_SESSION['tipoMensaje'] ?? '';
unset($_SESSION['mensaje'], $_SESSION['tipoMensaje']);

// Variables para el modo edición con tus nuevos campos bibliográficos
$modo_editar = false;
$id_libro = '';
$titulo = '';
$autor = '';
$encabezamiento_de_materia = '';
$editorial = '';
$isbn = '';
$ano = '';
$clasificacion = '';
$pais = '';
$idioma = '';
$descripcion = '';
$estado_conservacion = 'Excelente';
$ejemplares_totales = 1;

// 2. DETECTAR SI SE SOLICITA EDITAR UN REGISTRO (MÉTODO GET)
if (isset($_GET['editar_id'])) {
    $id_editar = filter_input(INPUT_GET, 'editar_id', FILTER_VALIDATE_INT);
    if ($id_editar) {
        $stmt_buscar = $conexion->prepare('SELECT * FROM libros WHERE id_libro = ?');
        $stmt_buscar->bind_param('i', $id_editar);
        $stmt_buscar->execute();
        $resultado_buscar = $stmt_buscar->get_result();
        if ($libro_data = $resultado_buscar->fetch_assoc()) {
            $modo_editar = true;
            $id_libro = $libro_data['id_libro'];
            $titulo = $libro_data['Titulo'];
            $autor = $libro_data['Autor'];
            $encabezamiento_de_materia = $libro_data['Encabezamiento_de_Materia'] ?? '';
            $editorial = $libro_data['Editorial'];
            $isbn = $libro_data['ISBN'];
            $ano = $libro_data['ano'] ?? '';
            $clasificacion = $libro_data['clasificacion'] ?? '';
            $pais = $libro_data['Pais'];
            $idioma = $libro_data['Idioma'];
            $descripcion = $libro_data['descripcion'] ?? '';
            $estado_conservacion = $libro_data['Estado_conservación'] ?? $libro_data['Estado_conservacion'] ?? 'Excelente';
            $ejemplares_totales = $libro_data['Ejemplares_totales'];
        }
        $stmt_buscar->close();
    }
}

// 3. PROCESAR EL FORMULARIO (REGISTRO O ACTUALIZACIÓN - MÉTODO POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_libro_post = filter_input(INPUT_POST, 'id_libro', FILTER_VALIDATE_INT);
    $titulo = trim($_POST['Titulo'] ?? '');
    $autor = trim($_POST['Autor'] ?? '');
    $encabezamiento_de_materia = trim($_POST['Encabezamiento_de_Materia'] ?? '');
    $editorial = trim($_POST['Editorial'] ?? '');
    $isbn = trim($_POST['ISBN'] ?? '');
    $ano = trim($_POST['ano'] ?? '');
    $clasificacion = trim($_POST['clasificacion'] ?? '');
    $pais = trim($_POST['Pais'] ?? '');
    $idioma = trim($_POST['Idioma'] ?? '');
    $descripcion = trim($_POST['descripcion'] ?? '');
    $estado_conservacion = trim($_POST['Estado_conservacion'] ?? 'Excelente');
    $ejemplares_totales = filter_input(INPUT_POST, 'Ejemplares_totales', FILTER_VALIDATE_INT);
    
    if ($titulo === '' || $autor === '' || $ejemplares_totales === false || $ejemplares_totales < 0) {
        $_SESSION['mensaje'] = 'El título, autor y la cantidad de ejemplares son obligatorios.';
        $_SESSION['tipoMensaje'] = 'error';
    } else {
        if (!empty($id_libro_post)) {
            // ACTUALIZAR LIBRO EXISTENTE EN EL CATÁLOGO (MODO EDICIÓN)
            $stmt_calc = $conexion->prepare('SELECT Ejemplares_prestados FROM libros WHERE id_libro = ?');
            $stmt_calc->bind_param('i', $id_libro_post);
            $stmt_calc->execute();
            $res_calc = $stmt_calc->get_result()->fetch_assoc();
            $prestados = $res_calc['Ejemplares_prestados'] ?? 0;
            $stmt_calc->close();

            $disponibles_nuevos = $ejemplares_totales - $prestados;
            if ($disponibles_nuevos < 0) $disponibles_nuevos = 0;

            $stmt = $conexion->prepare('UPDATE libros SET Titulo=?, Autor=?, Encabezamiento_de_Materia=?, Editorial=?, ISBN=?, ano=?, clasificacion=?, Pais=?, Idioma=?, descripcion=?, Estado_conservación=?, Ejemplares_totales=?, Ejemplares_disponibles=? WHERE id_libro=?');
            $stmt->bind_param('sssssssssssiii', $titulo, $autor, $encabezamiento_de_materia, $editorial, $isbn, $ano, $clasificacion, $pais, $idioma, $descripcion, $estado_conservacion, $ejemplares_totales, $disponibles_nuevos, $id_libro_post);
            $texto_exito = 'Libro actualizado en el catálogo correctamente.';
        } else {
            // INSERTAR NUEVO LIBRO AL CATÁLOGO
            $stmt = $conexion->prepare('INSERT INTO libros (Titulo, Autor, Encabezamiento_de_Materia, Editorial, ISBN, ano, clasificacion, Pais, Idioma, descripcion, Estado_conservación, Ejemplares_totales, Ejemplares_disponibles, Ejemplares_prestados) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 0)');
            $stmt->bind_param('sssssssssssii', $titulo, $autor, $encabezamiento_de_materia, $editorial, $isbn, $ano, $clasificacion, $pais, $idioma, $descripcion, $estado_conservacion, $ejemplares_totales, $ejemplares_totales);
            $texto_exito = 'Nuevo libro añadido al catálogo de la biblioteca.';
        }
        
        if ($stmt && $stmt->execute()) {
            $_SESSION['mensaje'] = $texto_exito;
            $_SESSION['tipoMensaje'] = 'exito';
            $stmt->close();
        } else {
            $_SESSION['mensaje'] = 'Error al guardar en el catálogo: ' . $conexion->error;
            $_SESSION['tipoMensaje'] = 'error';
        }
    }
    header("Location: ver_catalogo.php"); 
    exit();
}

// 4. PROCESAR EL BORRADO EN EL CATÁLOGO (MÉTODO GET)
if (isset($_GET['eliminar_id'])) {
    $id_a_borrar = filter_input(INPUT_GET, 'eliminar_id', FILTER_VALIDATE_INT);
    if ($id_a_borrar) {
        $stmt_borrar = $conexion->prepare('DELETE FROM libros WHERE id_libro = ?');
        if ($stmt_borrar) {
            $stmt_borrar->bind_param('i', $id_a_borrar); 
            if ($stmt_borrar->execute()) {
                $_SESSION['mensaje'] = 'Libro eliminado del catálogo correctamente.';
                $_SESSION['tipoMensaje'] = 'exito';
            } else {
                $_SESSION['mensaje'] = 'Error al eliminar del catálogo.';
                $_SESSION['tipoMensaje'] = 'error';
            }
            $stmt_borrar->close(); 
        } else {
            $_SESSION['mensaje'] = 'Error al preparar la consulta.';
            $_SESSION['tipoMensaje'] = 'error';
        }
    }
    header("Location: ver_catalogo.php"); 
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Catálogo Maestro - Libros</title>
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

  <div class="contenedor-formulario" style="max-width: 800px;">
      <h2><?php echo $modo_editar ? '📝 Modificar Libro del Catálogo' : '📚 Registro de Libros'; ?></h2>
      
      <form method="post" action="catalogo.php">
        <input type="hidden" name="id_libro" value="<?php echo $id_libro; ?>">

        <!-- Bloque de campos superiores en dos columnas balanceadas -->
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
            <div class="grupo-campo">
                <label>Título del Libro</label>
                <input type="text" name="Titulo" value="<?php echo htmlspecialchars($titulo); ?>" maxlength="255" required>
            </div>
            <div class="grupo-campo">
                <label>Autor</label>
                <input type="text" name="Autor" value="<?php echo htmlspecialchars($autor); ?>" maxlength="255" required>
            </div>
            <div class="grupo-campo">
                <label>Encabezamiento de Materia</label>
                <input type="text" name="Encabezamiento_de_Materia" value="<?php echo htmlspecialchars($encabezamiento_de_materia); ?>" maxlength="255">
            </div>
            <div class="grupo-campo">
                <label>Editorial</label>
                <input type="text" name="Editorial" value="<?php echo htmlspecialchars($editorial); ?>" maxlength="100">
            </div>
           <div class="grupo-campo">
                <label>ISBN / Código</label>
                <div style="display: flex; gap: 8px;">
                    <!-- Le ponemos el id="isbn_input" para que JavaScript lo encuentre -->
                    <input type="text" id="isbn_input" name="ISBN" value="<?php echo htmlspecialchars($isbn); ?>" maxlength="50" style="flex: 1;">
                    <!-- Botón express que activa la magia de Google Books -->
                    <button type="button" onclick="buscarLibroPorISBN()" style="background-color: #007bff; color: white; border: none; padding: 0 15px; border-radius: 4px; cursor: pointer; font-weight: bold;">🔍 Buscar</button>
                </div>
            </div>
            <div class="grupo-campo">
                <label>Año</label>
                <input type="number" name="ano" value="<?php echo htmlspecialchars($ano); ?>" max="9999">
            </div>
            <div class="grupo-campo">
                <label>Clasificación</label>
                <input type="text" name="clasificacion" value="<?php echo htmlspecialchars($clasificacion); ?>" maxlength="100">
            </div>
            <div class="grupo-campo">
                <label>País</label>
                <input type="text" name="Pais" value="<?php echo htmlspecialchars($pais); ?>" maxlength="50">
            </div>
             <!-- 🟢 IDIOMA: Al estar fuera del primer grid, se estira de lado a lado automáticamente -->
          <div class="grupo-campo" style="margin-bottom: 20px; width: 100%;">
              <label style="display: block; margin-bottom: 8px; font-weight: bold;">Idioma</label>
              <input type="text" name="Idioma" value="<?php echo htmlspecialchars($idioma); ?>" maxlength="50" style="width: 100%; box-sizing: border-box;">
          </div>

          <!-- 🟢 DESCRIPCIÓN: Ocupa todo el ancho completo -->
          <div class="grupo-campo" style="margin-bottom: 20px; width: 100%;">
              <label style="display: block; margin-bottom: 8px; font-weight: bold;">Descripción / Notas</label>
              <input type="text" name="descripcion" value="<?php echo htmlspecialchars($descripcion); ?>" maxlength="255" style="width: 100%; box-sizing: border-box;">
          </div>

          <!-- 🟢 EJEMPLARES TOTALES: Ocupa todo el ancho completo -->
          <div class="grupo-campo" style="margin-bottom: 25px; width: 100%;">
              <label style="display: block; margin-bottom: 8px; font-weight: bold;">Ejemplares Totales</label>
              <input type="number" name="Ejemplares_totales" value="<?php echo htmlspecialchars($ejemplares_totales); ?>" min="1" required style="width: 100%; box-sizing: border-box;">
          </div>

          <!-- 🟢 TU BOTÓN ORIGINAL: Libre de las columnas, abarcando el 100% de forma limpia -->
          <button type="submit" class="boton-guardar" style="width: 100%; padding: 14px; font-size: 15px; font-weight: bold; cursor: pointer; box-sizing: border-box;">
              <?php echo $modo_editar ? 'Actualizar Libro e Inventario' : 'REGISTRAR LIBRO'; ?>
          </button>

                    <div style="margin-top: 20px; width: 100%;">
              <a href="ver_catalogo.php" style="display: block; text-align: center; font-weight: bold; color: white; text-decoration: none; background-color: #28a745; padding: 12px; border-radius: 5px; box-sizing: border-box;">🔍 Ver Todos los Libros Registrados</a>
          </div>

        </div> <!-- Cierra el div del grid -->
      </form> <!-- 🟢 TRUCO MAESTRO 1: Cerramos el formulario aquí para liberar las columnas -->
  </div> <!-- 🟢 TRUCO MAESTRO 2: Cerramos la tarjeta blanca original aquí -->

  <!-- 🚀 SEGUNDO BLOQUE INDEPENDIENTE: Genera una nueva tarjeta limpia justo abajo para tus 3 campos extendidos -->
  <div class="contenedor-formulario" style="max-width: 800px; margin: -10px auto 20px auto; padding: 20px; box-sizing: border-box; background: #fff; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.05);">
      
      <!-- Abrimos un segundo formulario que apunta al mismo archivo para que procese los datos -->
      <form method="post" action="catalogo.php">
          <!-- Pasamos los datos ocultos para que PHP no pierda el hilo si estás editando -->
          <input type="hidden" name="id_libro" value="<?php echo $id_libro; ?>">
          <input type="hidden" name="Titulo" value="<?php echo htmlspecialchars($titulo); ?>">
          <input type="hidden" name="Autor" value="<?php echo htmlspecialchars($autor); ?>">
          <input type="hidden" name="Encabezamiento_de_Materia" value="<?php echo htmlspecialchars($encabezamiento_de_materia); ?>">
          <input type="hidden" name="Editorial" value="<?php echo htmlspecialchars($editorial); ?>">
          <input type="hidden" name="ISBN" value="<?php echo htmlspecialchars($isbn); ?>">
          <input type="hidden" name="ano" value="<?php echo htmlspecialchars($ano); ?>">
          <input type="hidden" name="clasificacion" value="<?php echo htmlspecialchars($clasificacion); ?>">
          <input type="hidden" name="Pais" value="<?php echo htmlspecialchars($pais); ?>">

         
          <?php if ($modo_editar): ?>
              <a href="catalogo.php" style="display: block; text-align: center; margin-top: 15px; color: #721c24; text-decoration: none; font-size: 14px; font-weight: bold;">Cancelar Edición</a>
          <?php endif; ?>


      </form>
  </div> 

 
  <div class="z1">
      <a class="z2" href="registro.html">REGRESAR</a>
  </div>

</body>
</html>