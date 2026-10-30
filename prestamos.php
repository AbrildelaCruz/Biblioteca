<?php
require_once 'conexion.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$mensaje = $_SESSION['mensaje'] ?? '';
$tipoMensaje = $_SESSION['tipoMensaje'] ?? '';
unset($_SESSION['mensaje'], $_SESSION['tipoMensaje']);

// ==========================================
// 1. PROCESAR ACCIÓN: REGISTRAR DEVOLUCIÓN
// ==========================================
if (isset($_GET['devolver_id'])) {
    $id_prestamo_dev = filter_input(INPUT_GET, 'devolver_id', FILTER_VALIDATE_INT);

    if ($id_prestamo_dev) {
        $fecha_real = date('Y-m-d');

        // Buscar qué libro es para devolverlo al inventario
        $stmt_buscar = $conexion->prepare("SELECT id_libro FROM prestamos WHERE id_prestamo = ?");
        $stmt_buscar->bind_param("i", $id_prestamo_dev);
        $stmt_buscar->execute();
        $res_buscar = $stmt_buscar->get_result()->fetch_assoc();
        $stmt_buscar->close();

        if ($res_buscar) {
            $id_libro_update = $res_buscar['id_libro'];

            // Marcar como devuelto
            $stmt_dev = $conexion->prepare("UPDATE prestamos SET fecha_devolucion_real = ?, estado_libro = 'Excelente' WHERE id_prestamo = ?");
            $stmt_dev->bind_param("si", $fecha_real, $id_prestamo_dev);

            if ($stmt_dev->execute()) {
                // Sumar 1 disponible y restar 1 prestado en la tabla de libros
                $stmt_upd_lib = $conexion->prepare("UPDATE libros SET Ejemplares_disponibles = Ejemplares_disponibles + 1, Ejemplares_prestados = GREATEST(0, Ejemplares_prestados - 1) WHERE id_libro = ?");
                $stmt_upd_lib->bind_param("i", $id_libro_update);
                $stmt_upd_lib->execute();
                $stmt_upd_lib->close();

                $_SESSION['mensaje'] = "🟢 Devolución asentada con éxito. El libro ha regresado al inventario disponible.";
                $_SESSION['tipoMensaje'] = 'exito';
            } else {
                $_SESSION['mensaje'] = "Error al asentar la devolución en la base de datos.";
                $_SESSION['tipoMensaje'] = 'error';
            }
            $stmt_dev->close();
        }
    }
    // Redirige a este mismo archivo
    header("Location: prestamos.php");
    exit();
}

// ==========================================
// 2. CONSULTAR EL HISTORIAL DE PRÉSTAMOS
// ==========================================
$sql_bitacora = "SELECT 
                    p.id_prestamo,
                    l.Titulo AS Libro,
                    COALESCE(a.Nombre, prof.Nombre, 'Desconocido') AS Persona,
                    CASE WHEN a.Nombre IS NOT NULL THEN 'Alumno' ELSE 'Profesor' END AS TipoPersona,
                    p.fecha_salida,
                    p.fecha_devolucion_esperada,
                    p.fecha_devolucion_real,
                    p.tipo_movimiento
                 FROM prestamos p
                 JOIN libros l ON p.id_libro = l.id_libro
                 LEFT JOIN alumnos a ON p.id_alumnos = a.id_Alumnos
                 LEFT JOIN profesores prof ON p.id_profesores = prof.id_profesores
                 ORDER BY p.id_prestamo DESC";

$resultado_bitacora = $conexion->query($sql_bitacora);
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bitácora General de Préstamos - Biblioteca Sullivan</title>
    <link rel="stylesheet" href="style.css?v=<?php echo time(); ?>">
</head>

<body class="body2">

    <div class="contenedor-burbujas">
        <div class="burbuja"></div>
        <div class="burbuja"></div>
        <div class="burbuja"></div>
    </div>

    <?php if ($mensaje !== ''): ?>
        <div class="alerta <?php echo $tipoMensaje; ?>"
            style="padding: 10px; margin: 15px auto; max-width: 850px; text-align: center; border-radius: 5px; font-weight: bold; background-color: <?php echo $tipoMensaje === 'exito' ? '#d4edda' : '#f8d7da'; ?>; color: <?php echo $tipoMensaje === 'exito' ? '#155724' : '#721c24'; ?>;">
            <?php echo $mensaje; ?>
        </div>
    <?php endif; ?>

    <h2 style="text-align: center; color: #0F4C81; margin-top: 30px;">📋 Bitácora General de Préstamos y Apartados</h2>
    <p style="text-align: center; color: #475569; margin-bottom: 20px;">Cuaderno digital de control para registrar
        devoluciones y revisar el historial.</p>

    <table class="tabla-datos" style="max-width: 95%; margin: 20px auto; font-size: 13px;">
        <thead>
            <tr>
                <th>ID</th>
                <th>Libro Solicitado</th>
                <th>Usuario</th>
                <th>Ocupación</th>
                <th>Tipo</th>
                <th>Fecha Salida</th>
                <th>Fecha Límite</th>
                <th>Fecha Devolución</th>
                <th>Acción</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($resultado_bitacora && $resultado_bitacora->num_rows > 0): ?>
                <?php while ($fila = $resultado_bitacora->fetch_assoc()):
                    $entregado = ($fila['fecha_devolucion_real'] !== null);
                    $es_apartado = ($fila['tipo_movimiento'] === 'Apartado');
                    ?>
                    <tr>
                        <td style="font-weight: bold; text-align: center;"><?php echo $fila['id_prestamo']; ?></td>
                        <td><b><?php echo htmlspecialchars($fila['Libro']); ?></b></td>
                        <td><u><?php echo htmlspecialchars($fila['Persona']); ?></u></td>
                        <td><?php echo $fila['TipoPersona']; ?></td>
                        <td>
                            <span
                                style="padding: 2px 6px; border-radius: 3px; font-size: 11px; font-weight: bold; background-color: <?php echo $es_apartado ? '#FFFBEB' : '#EFF6FF'; ?>; color: <?php echo $es_apartado ? '#92400E' : '#1E40AF'; ?>;">
                                <?php echo htmlspecialchars($fila['tipo_movimiento'] ?? 'Prestado'); ?>
                            </span>
                        </td>
                        <td><?php echo date('d/m/Y', strtotime($fila['fecha_salida'])); ?></td>
                        <td><?php echo date('d/m/Y', strtotime($fila['fecha_devolucion_esperada'])); ?></td>
                        <td>
                            <?php if ($entregado): ?>
                                <span style="color: green; font-weight: bold;">✔
                                    <?php echo date('d/m/Y', strtotime($fila['fecha_devolucion_real'])); ?></span>
                            <?php else: ?>
                                <span style="color: #94A3B8; font-style: italic;">Pendiente</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if (!$entregado): ?>
                                <a href="prestamos.php?devolver_id=<?php echo $fila['id_prestamo']; ?>"
                                    style="text-decoration: none; padding: 4px 8px; background-color: #2A9D8F; border-radius: 4px; color: white; font-size: 11px; font-weight: bold; display: inline-block;"
                                    onclick="return confirm('¿Confirmas que el libro ha sido devuelto físicamente?');">
                                    📥 Recibir
                                </a>
                            <?php else: ?>
                                <span style="color: #475569; font-size: 11px; font-weight: bold;">Completado</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="9" style="text-align: center; font-weight: bold; padding: 20px;">No se ha registrado ningún
                        préstamo o apartado en el sistema todavía.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <div class="z1">
        <a class="z2" href="registro.html">REGRESAR</a>
    </div>
</body>

</html>