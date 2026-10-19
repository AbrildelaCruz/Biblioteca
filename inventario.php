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
    <link rel="stylesheet" href="style.css?v=<?php echo time(); ?>">
    <link rel="icon" type="image/png" href="logobiblioteca.png">
    <title>Inventario</title>
</head>
<body class="body2"> <!-- Mismo fondo animado y centrado de los anteriores -->

    <!-- Animación de burbujas de fondo -->
    <div class="contenedor-burbujas">
        <div class="burbuja"></div>
        <div class="burbuja"></div>
        <div class="burbuja"></div>
        <div class="burbuja"></div>
        <div class="burbuja"></div>
        <div class="burbuja"></div>
    </div>

    <!-- Mostrar alerta flotante si hay mensajes -->
    <?php if ($mensaje !== ''): ?>
        <div class="alerta <?php echo $tipoMensaje; ?>" style="padding: 10px; margin: 15px auto; max-width: 700px; text-align: center; border-radius: 5px; font-weight: bold; background-color: <?php echo $tipoMensaje === 'exito' ? '#d4edda' : '#f8d7da'; ?>; color: <?php echo $tipoMensaje === 'exito' ? '#155724' : '#721c24'; ?>; z-index: 10; position: relative;">
            <?php echo $mensaje; ?>
        </div>
    <?php endif; ?>

    <!-- Contenedor del Formulario (Alineado con tus pantallas anteriores) -->
    <div class="contenedor-formulario">
        <h2>📦 Control de Inventario</h2> <!-- Añadido un toque visual diferente -->
         
        <form method="post" action="inventario.php">
            <div class="grupo-campo">
                <label>ID del Libro / Código</label>
                <input type="text" name="id_libro" maxlength="20" required placeholder="Ej. L-402">
            </div>
            
            <div class="grupo-campo">
                <label>Estado de Conservación</label>
                <input type="text" name="estado_conservacion" maxlength="50" required placeholder="Ej. Excelente, Bueno, Dañado">
            </div>
            
            <!-- El botón ahora se integra perfectamente en su propia fila abajo -->
            <button type="submit" class="boton-guardar" style="grid-column: 1 / -1;">Guardar Registro</button>
        </form>
    </div>
       
    <!-- Tabla de datos estilizada idéntica a las anteriores -->
    <table class="tabla-datos">
        <thead>
            <tr>
                <th>ID Inventario</th>
                <th>ID Libro</th>
                <th>Estado de Conservación</th>
                <th>Acción</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            if ($resultado && $resultado->num_rows > 0): 
                while ($fila = $resultado->fetch_assoc()): 
            ?>
                <tr>
                    <td><?php echo $fila['id_inventario']; ?></td>
                    <td><?php echo htmlspecialchars($fila['id_libro']); ?></td>
                    <td><?php echo htmlspecialchars($fila['Estado_conservación']); ?></td>
                    <!-- Botón de borrado permanente -->
                    <td>
                        <a href="inventario.php?eliminar_id=<?php echo $fila['id_inventario']; ?>" style="text-decoration: none; display: inline-block; padding: 5px 10px; background-color: #fff; border: 1px solid #ccc; border-radius: 3px; color: black; font-size: 13px;" onclick="return confirm('¿Seguro que deseas eliminar este registro de inventario permanentemente?');">Eliminar</a>
                    </td>
                </tr>
            <?php 
                endwhile; 
            else: 
            ?>
                <tr>
                    <td colspan="4" style="text-align: center;">No hay registros en el inventario actualmente.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <!-- Botón regresar fijo abajo a la derecha -->
    <div class="z1">
        <a class="z2" href="registro.html">REGRESAR</a>
    </div>
  
</body>  
</html>