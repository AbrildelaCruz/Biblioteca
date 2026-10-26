<?php
require_once 'conexion.php';

// 1. RECUPERAR ALERTAS DE LA SESIÓN TRAS GUARDAR O ELIMINAR
$mensaje = $_SESSION['mensaje'] ?? '';
$tipoMensaje = $_SESSION['tipoMensaje'] ?? '';
unset($_SESSION['mensaje'], $_SESSION['tipoMensaje']);

// 2. CONSULTAR TODOS LOS REGISTROS DE PROFESORES ORDENADOS POR TU COLUMNA REAL (id_profesores)
$resultado = $conexion->query('SELECT * FROM profesores ORDER BY id_profesores DESC');
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Profesores</title>
    <link rel="stylesheet" href="style.css?v=<?php echo time(); ?>">
    <link rel="icon" type="image/png" href="logobiblioteca.png">
</head>
<body class="body2">

    <div class="contenedor-burbujas">
        <div class="burbuja"></div>
        <div class="burbuja"></div>
        <div class="burbuja"></div>
    </div>

    <?php if ($mensaje !== ''): ?>
    <div class="alerta <?php echo $tipoMensaje; ?>" style="padding: 10px; margin: 15px auto; max-width: 700px; text-align: center; border-radius: 5px; font-weight: bold; background-color: <?php echo $tipoMensaje === 'exito' ? '#d4edda' : '#f8d7da'; ?>; color: <?php echo $tipoMensaje === 'exito' ? '#155724' : '#721c24'; ?>;">
        <?php echo $mensaje; ?>
    </div>
    <?php endif; ?>

    <h2 style="text-align: center; color: white; margin-top: 30px;">👨‍🏫 Panel de Control - Profesores de la Institución</h2>

    <table class="tabla-datos" style="max-width: 85%; margin: 30px auto; font-size: 13px;">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre del Profesor</th>
                <th>Materia(s) Asignada(s)</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($resultado && $resultado->num_rows > 0): 
                while ($fila = $resultado->fetch_assoc()): 
                    // Obtener la llave primaria real en plural
                    $id_actual = $fila['id_profesores'];
                    $materias_docente = $fila['Materia'] ?? $fila['materia'] ?? '-';
                ?>
                <tr>
                    <td><?php echo $id_actual; ?></td>
                    <td><b><?php echo htmlspecialchars($fila['Nombre'] ?? $fila['nombre'] ?? ''); ?></b></td>
                    <td><span style="color: #4a148c; font-weight: 500;"><?php echo htmlspecialchars($materias_docente); ?></span></td>
                    <td>
                        <a href="profesores.php?editar_id=<?php echo $id_actual; ?>" style="text-decoration: none; padding: 3px 6px; background-color: #007bff; border-radius: 3px; color: white; font-size: 11px; margin-right: 5px;">Editar</a>
                        <a href="profesores.php?eliminar_id=<?php echo $id_actual; ?>" style="text-decoration: none; padding: 3px 6px; background-color: #f8d7da; border-radius: 3px; color: #721c24; font-size: 11px;" onclick="return confirm('¿Seguro que deseas eliminar este profesor?');">Eliminar</a>
                    </td>
                </tr>
            <?php endwhile; 
            else: ?>
                <tr>
                    <td colspan="4" style="text-align: center;">No hay profesores registrados actualmente.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <div style="text-align: center; margin-bottom: 40px;">
        <a href="profesores.php" style="display: inline-block; padding: 10px 20px; background-color: #007bff; color: white; text-decoration: none; border-radius: 5px; font-weight: bold;">➕ Registrar Nuevo Profesor</a>
    </div>

    <div class="z1">
        <a class="z2" href="registro.html">REGRESAR</a>
    </div>
</body>
</html>