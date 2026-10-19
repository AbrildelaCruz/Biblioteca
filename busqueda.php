<?php
require_once 'conexion.php';

// 1. RECUPERAR MENSAJES DE LA SESIÓN SI EXISTEN PARA EVITAR DUPLICADOS
$mensaje = $_SESSION['mensaje'] ?? '';
$tipoMensaje = $_SESSION['tipoMensaje'] ?? '';

unset($_SESSION['mensaje'], $_SESSION['tipoMensaje']);

// 2. PROCESAR EL REGISTRO DE INVENTARIO (MÉTODO POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $idLibro = trim($_POST['id_libro'] ?? '');
    $estadoConservacion = trim($_POST['estado_conservacion'] ?? '');

    if ($idLibro === '' || $estadoConservacion === '') {
        $_SESSION['mensaje'] = 'Completa todos los campos obligatorios.';
        $_SESSION['tipoMensaje'] = 'error';
    } else {
        $stmt = $conexion->prepare(
            'INSERT INTO inventario (id_libro, Estado_conservación) VALUES (?, ?)'
        );
        
        if (!$stmt) {
            $_SESSION['mensaje'] = 'No se pudo preparar el guardado: ' . $conexion->error;
            $_SESSION['tipoMensaje'] = 'error';
        } else {
            $stmt->bind_param('ss', $idLibro, $estadoConservacion);
            if ($stmt->execute()) {
                $_SESSION['mensaje'] = 'Datos guardados en el inventario correctamente.';
                $_SESSION['tipoMensaje'] = 'exito';
            } else {
                $_SESSION['mensaje'] = 'No se guardaron los datos: ' . $stmt->error;
                $_SESSION['tipoMensaje'] = 'error';
            }
            $stmt->close();
        }
    }
    // REDIRECCIÓN OBLIGATORIA: Limpia el POST para que no se duplique con F5
    header("Location: inventario.php");
    exit();
}

// 3. PROCESAR EL BORRADO REAL EN LA BASE DE DATOS (MÉTODO GET)
if (isset($_GET['eliminar_id'])) {
    $id_a_borrar = filter_input(INPUT_GET, 'eliminar_id', FILTER_VALIDATE_INT);
    if ($id_a_borrar) {
        $stmt_borrar = $conexion->prepare('DELETE FROM inventario WHERE id_inventario = ?');
        if ($stmt_borrar) {
            $stmt_borrar->bind_param('i', $id_a_borrar);
            if ($stmt_borrar->execute()) {
                $_SESSION['mensaje'] = 'Registro de inventario eliminado correctamente.';
                $_SESSION['tipoMensaje'] = 'exito';
            } else {
                $_SESSION['mensaje'] = 'Error al eliminar en la base de datos.';
                $_SESSION['tipoMensaje'] = 'error';
            }
            $stmt_borrar->close();
        }
    }
    header("Location: inventario.php");
    exit();
}

// 4. CONSULTAR LOS DATOS ACTUALIZADOS
$resultado = $conexion->query(
    'SELECT id_inventario, id_libro, Estado_conservación FROM inventario ORDER BY id_inventario DESC'
);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Biblioteca Sullivan - Búsqueda</title>
    <link rel="stylesheet" href="style.css?v=<?php echo time(); ?>">
    <link rel="icon" type="image/png" href="logobiblioteca.png">
    <style>
        /* Cuadrícula organizada en el formulario para acomodar tus 4 campos */
        .contenedor-formulario fieldset {
            border: none;
            padding: 0;
            margin: 0;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 15px;
            width: 100%;
        }
    </style>
</head>
<body class="body2"> <!-- Llama a tu fondo animado con degradado y scroll limpio -->

    <!-- Animación de burbujas flotantes de fondo -->
    <div class="contenedor-burbujas">
        <div class="burbuja"></div>
        <div class="burbuja"></div>
        <div class="burbuja"></div>
        <div class="burbuja"></div>
        <div class="burbuja"></div>
        <div class="burbuja"></div>
    </div>

    <!-- Bloque del Buscador Estilizado como tus formularios de Profesores e Inventario -->
    <div class="contenedor-formulario" style="max-width: 850px; margin-top: 30px;">
        <h2>🔍 Biblioteca Sullivan</h2>
        
        <!-- Apunta exactamente a Busqueda.php respetando la mayúscula inicial -->
        <form method="get" action="Busqueda.php">
            <fieldset>
                <input type="search" id="buscar-titulo" name="titulo" value="<?php echo htmlspecialchars($titulo); ?>" placeholder="Título del libro...">
                <input type="search" id="buscar-tema" name="tema" value="<?php echo htmlspecialchars($tema); ?>" placeholder="Categoría o Tema...">
                <input type="search" id="buscar-autor" name="autor" value="<?php echo htmlspecialchars($autor); ?>" placeholder="Autor...">
                <input type="search" id="buscar-isbn" name="isbn" value="<?php echo htmlspecialchars($isbn); ?>" placeholder="Código / ID del Libro...">
                
                <button type="submit" class="boton-guardar" style="grid-column: 1 / -1; margin-top: 10px;">🔍 Realizar Búsqueda</button> 
            </fieldset>
        </form>
    </div>

    <!-- Tabla Dinámica con tus 10 Columnas Reales -->
    <table class="tabla-datos" style="max-width: 1100px; margin-top: 25px;">
        <thead>
            <tr>
                <th>ID</th>
                <th>Título</th>
                <th>Autor</th>
                <th>Categoría</th>
                <th>Editorial</th>
                <th>Edición</th>
                <th>Idioma</th>
                <th>Disponibles</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($resultados && $resultados->num_rows > 0): ?>
                <?php while ($fila = $resultados->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $fila['id_libro']; ?></td>
                        <td><strong><?php echo htmlspecialchars($fila['Titulo']); ?></strong></td>
                        <td><?php echo htmlspecialchars($fila['Autor']); ?></td>
                        <!-- Imprime tu columna Categoría con el acento exacto de tu base de datos -->
                        <td><?php echo htmlspecialchars($fila['Categoría']); ?></td>
                        <td><?php echo htmlspecialchars($fila['Editorial']); ?></td>
                        <td><?php echo htmlspecialchars($fila['Edición']); ?></td>
                        <td><?php echo htmlspecialchars($fila['Idioma']); ?></td>
                        <!-- Indicador visual dinámico de existencias -->
                        <td>
                            <span style="font-weight: bold; color: <?php echo $fila['Ejemplares_disponibles'] > 0 ? '#16753b' : '#b00020'; ?>;">
                                <?php echo $fila['Ejemplares_disponibles']; ?> de <?php echo $fila['Ejemplares_totales']; ?>
                            </span>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="8" style="text-align: center; font-weight: bold; color: #b00020; padding: 20px;">
                        ⚠️ No se encontraron libros que coincidan con los filtros ingresados.
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
           
    <!-- Botón flotante para regresar abajo a la derecha -->
    <div class="z1">
        <a class="z2" href="registro.html">REGRESAR</a>
    </div>

</body>
</html>
     