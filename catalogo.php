<?php
require_once 'conexion.php';

// 1. RECUPERAR MENSAJES DE LA SESIÓN
$mensaje = $_SESSION['mensaje'] ?? '';
$tipoMensaje = $_SESSION['tipoMensaje'] ?? '';
unset($_SESSION['mensaje'], $_SESSION['tipoMensaje']);

// Variables para el modo edición de un libro del catálogo
$modo_editar = false;
$id_libro = '';
$titulo = '';
$autor = '';
$categoria = '';
$editorial = '';
$edicion = '';
$isbm = '';
$pais = '';
$idioma = '';
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
            $categoria = $libro_data['Categoria'] ?? $libro_data['Categoría'] ?? '';
            $editorial = $libro_data['Editorial'];
            $edicion = $libro_data['Edicion'] ?? $libro_data['Edición'] ?? '';
            $isbm = $libro_data['ISBM'];
            $pais = $libro_data['Pais'];
            $idioma = $libro_data['Idioma'];
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
    $categoria = trim($_POST['Categoria'] ?? '');
    $editorial = trim($_POST['Editorial'] ?? '');
    $edicion = trim($_POST['Edicion'] ?? '');
    $isbm = trim($_POST['ISBM'] ?? '');
    $pais = trim($_POST['Pais'] ?? '');
    $idioma = trim($_POST['Idioma'] ?? '');
    $ejemplares_totales = filter_input(INPUT_POST, 'Ejemplares_totales', FILTER_VALIDATE_INT);
    
    if ($titulo === '' || $autor === '' || $ejemplares_totales === false || $ejemplares_totales < 0) {
        $_SESSION['mensaje'] = 'El título, autor y la cantidad de ejemplares son obligatorios.';
        $_SESSION['tipoMensaje'] = 'error';
    } else {
        if (!empty($id_libro_post)) {
            // ACTUALIZAR LIBRO EXISTENTE EN EL CATÁLOGO
            $stmt_calc = $conexion->prepare('SELECT Ejemplares_prestados FROM libros WHERE id_libro = ?');
            $stmt_calc->bind_param('i', $id_libro_post);
            $stmt_calc->execute();
            $res_calc = $stmt_calc->get_result()->fetch_assoc();
            $prestados = $res_calc['Ejemplares_prestados'] ?? 0;
            $stmt_calc->close();

            $disponibles_nuevos = $ejemplares_totales - $prestados;
            if ($disponibles_nuevos < 0) $disponibles_nuevos = 0;

            $stmt = $conexion->prepare('UPDATE libros SET Titulo=?, Autor=?, Categoria=?, Editorial=?, Edicion=?, ISBM=?, Pais=?, Idioma=?, Ejemplares_totales=?, Ejemplares_disponibles=? WHERE id_libro=?');
            $stmt->bind_param('ssssssssiii', $titulo, $autor, $categoria, $editorial, $edicion, $isbm, $pais, $idioma, $ejemplares_totales, $disponibles_nuevos, $id_libro_post);
            $texto_exito = 'Libro actualizado en el catálogo correctamente.';
        } else {
            // INSERTAR NUEVO LIBRO AL CATÁLOGO
            $stmt = $conexion->prepare('INSERT INTO libros (Titulo, Autor, Categoria, Editorial, Edicion, ISBM, Pais, Idioma, Ejemplares_totales, Ejemplares_disponibles, Ejemplares_prestados) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 0)');
            $stmt->bind_param('ssssssssii', $titulo, $autor, $categoria, $editorial, $edicion, $isbm, $pais, $idioma, $ejemplares_totales, $ejemplares_totales);
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
    header("Location: catalogo.php");
    exit();
}

// 4. PROCESAR EL BORRADO EN EL CATÁLOGO (MÉTODO GET)
if (isset($_GET['eliminar_id'])) {
    $id_a_borrar = filter_input(INPUT_GET, 'eliminar_id', FILTER_VALIDATE_INT);
    if ($id_a_borrar) {
        $stmt_borrar = $conexion->prepare('DELETE FROM libros WHERE id_libro = ?');
        if ($stmt_borrar && $stmt_borrar->execute()) {
            $_SESSION['mensaje'] = 'Libro eliminado del catálogo correctamente.';
            $_SESSION['tipoMensaje'] = 'exito';
            $stmt_borrar->close();
        } else {
            $_SESSION['mensaje'] = 'Error al eliminar del catálogo.';
            $_SESSION['tipoMensaje'] = 'error';
        }
    }
    header("Location: catalogo.php");
    exit();
}

// 5. CONSULTAR LOS DATOS ACTUALIZADOS DEL CATÁLOGO
$resultado = $conexion->query('SELECT * FROM libros ORDER BY id_libro DESC');
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
      <div class="alerta <?php echo $tipoMensaje; ?>" style="padding: 10px; margin: 15px auto; max-width: 800px; text-align: center; border-radius: 5px; font-weight: bold; background-color: <?php echo $tipoMensaje === 'exito' ? '#d4edda' : '#f8d7da'; ?>; color: <?php echo $tipoMensaje === 'exito' ? '#155724' : '#721c24'; ?>;">
          <?php echo $mensaje; ?>
      </div>
  <?php endif; ?>

  <div class="contenedor-formulario" style="max-width: 750px;">
      <h2><?php echo $modo_editar ? '📝 Modificar Libro del Catálogo' : '📚 Alta de Libros (Catálogo)'; ?></h2>
      
      <form method="post" action="catalogo.php">
        <input type="hidden" name="id_libro" value="<?php echo $id_libro; ?>">

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
                <label>Categoría / Tema</label>
                <input type="text" name="Categoria" value="<?php echo htmlspecialchars($categoria); ?>" maxlength="100">
            </div>
            <div class="grupo-campo">
                <label>Editorial</label>
                <input type="text" name="Editorial" value="<?php echo htmlspecialchars($editorial); ?>" maxlength="100">
            </div>
            <div class="grupo-campo">
                <label>Edición</label>
                <input type="text" name="Edicion" value="<?php echo htmlspecialchars($edicion); ?>" maxlength="50">
            </div>
            <div class="grupo-campo">
                <label>ISBM / Código</label>
                <input type="text" name="ISBM" value="<?php echo htmlspecialchars($isbm); ?>" maxlength="50">
            </div>
            <div class="grupo-campo">
                <label>País</label>
                <input type="text" name="Pais" value="<?php echo htmlspecialchars($pais); ?>" maxlength="50">
            </div>
            <div class="grupo-campo">
                <label>Idioma</label>
                <input type="text" name="Idioma" value="<?php echo htmlspecialchars($idioma); ?>" maxlength="50">
            </div>
            <div class="grupo-campo" style="grid-column: span 2;">
                <label>Cantidad de Ejemplares Disponibles en Biblioteca</label>
                <input type="number" name="Ejemplares_totales" value="<?php echo $ejemplares_totales; ?>" min="0" required>
            </div>
        </div>

        <button type="submit" class="boton-guardar" style="margin-top: 15px; width: 100%;">
            <?php echo $modo_editar ? 'Actualizar Catálogo' : 'Guardar en Catálogo'; ?>
        </button>
        
        <?php if ($modo_editar): ?>
            <a href="catalogo.php" style="display: block; text-align: center; margin-top: 10px; color: #721c24; text-decoration: none; font-weight: bold;">❌ Cancelar Edición</a>
        <?php endif; ?>
      </form>
  </div>

  <table class="tabla-datos" style="max-width: 1200px; margin-top: 25px;">
    <thead>
      <tr>
        <th>ID</th>
        <th>Título</th>
        <th>Autor</th>
        <th>Categoría</th>
        <th>Editorial</th>
        <th>ISBM</th>
        <th>Cant. Total</th>
        <th>Disponibles</th>
        <th>Acciones</th>
      </tr>
    </thead>
    <tbody>
    <div class="z1">
        <a class="z2" href="registro.html">REGRESAR</a>
    </div>
      <?php
    if ($resultado && $resultado->num_rows > 0) {
        while($fila = $resultado->fetch_assoc()) {
            $id_act = (int)$fila['id_libro'];
            $cat = $fila['Categoria'] ?? $fila['Categoría'] ?? 'N/A';
            echo "<tr>";
            echo "<td>" . $id_act . "</td>";
            echo "<td><strong>" . htmlspecialchars($fila['Titulo']) . "</strong></td>";

echo "" . htmlspecialchars($fila['Autor']) . "";
echo "" . htmlspecialchars($cat) . "";
echo "" . htmlspecialchars($fila['Editorial'] ?? 'N/A') . "";
echo "" . htmlspecialchars($fila['ISBM'] ?? 'N/A') . "";
echo "" . (int)$fila['Ejemplares_totales'] . "";
echo "<span style='font-weight:bold; color:".($fila['Ejemplares_disponibles'] > 0 ? '#16753b':'#b00020')."'>" . (int)$fila['Ejemplares_disponibles'] . "";
echo "";
echo 'Editar';
echo 'Eliminar';
echo "";
echo "";
}
} else {
echo 'No hay libros registrados en el catálogo actualmente.';
}
?>