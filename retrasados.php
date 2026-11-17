<?php
require_once 'conexion.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$mensaje = $_SESSION['mensaje'] ?? '';
$tipoMensaje = $_SESSION['tipoMensaje'] ?? '';
unset($_SESSION['mensaje'], $_SESSION['tipoMensaje']);

date_default_timezone_set('America/Mexico_City');

// ===================================================
// 1. PROCESAR DEVOLUCIÓN DIRECTA DESDE RETRASADOS
// ===================================================
if (isset($_GET['devolver_id'])) {
    $id_prestamo_dev = filter_input(INPUT_GET, 'devolver_id', FILTER_VALIDATE_INT);

    if ($id_prestamo_dev) {
        $fecha_real = date('Y-m-d');

        $stmt_buscar = $conexion->prepare("SELECT id_libro FROM prestamos WHERE id_prestamo = ?");
        $stmt_buscar->bind_param("i", $id_prestamo_dev);
        $stmt_buscar->execute();
        $res_buscar = $stmt_buscar->get_result()->fetch_assoc();
        $stmt_buscar->close();

        if ($res_buscar) {
            $id_libro_update = $res_buscar['id_libro'];

            $stmt_dev = $conexion->prepare("UPDATE prestamos SET fecha_devolucion_real = ?, estado_libro = 'Excelente' WHERE id_prestamo = ?");
            $stmt_dev->bind_param("si", $fecha_real, $id_prestamo_dev);

            if ($stmt_dev->execute()) {
                $stmt_upd_lib = $conexion->prepare("UPDATE libros SET Ejemplares_disponibles = Ejemplares_disponibles + 1, Ejemplares_prestados = GREATEST(0, Ejemplares_prestados - 1) WHERE id_libro = ?");
                $stmt_upd_lib->bind_param("i", $id_libro_update);
                $stmt_upd_lib->execute();
                $stmt_upd_lib->close();

                $_SESSION['mensaje'] = "🟢 Libro recibido con éxito. Se quitó de la lista de retrasados y regresó al inventario.";
                $_SESSION['tipoMensaje'] = 'exito';
            } else {
                $_SESSION['mensaje'] = "Error al registrar la devolución.";
                $_SESSION['tipoMensaje'] = 'error';
            }
            $stmt_dev->close();
        }
    }
    header("Location: retrasados.php");
    exit();
}

// ===================================================
// 2. CONSULTAR PRÉSTAMOS CON 1 DÍA O MÁS DE RETRASO
// ===================================================
// DATEDIFF(CURDATE(), p.fecha_devolucion_esperada) >= 1 detecta desde 1 día vencido
$sql_retrasados = "SELECT 
                        p.id_prestamo,
                        l.Titulo AS Libro,
                        COALESCE(a.Nombre, prof.Nombre, 'Desconocido') AS Persona,
                        a.Matricula AS MatriculaAlumno,
                        CASE WHEN a.Nombre IS NOT NULL THEN 'Alumno' ELSE 'Profesor' END AS Rol,
                        p.fecha_salida,
                        p.fecha_devolucion_esperada,
                        DATEDIFF(CURDATE(), p.fecha_devolucion_esperada) AS dias_retraso
                   FROM prestamos p
                   JOIN libros l ON p.id_libro = l.id_libro
                   LEFT JOIN alumnos a ON p.id_alumnos = a.id_Alumnos
                   LEFT JOIN profesores prof ON p.id_profesores = prof.id_profesores
                   WHERE p.fecha_devolucion_real IS NULL
                     AND p.fecha_devolucion_esperada < CURDATE()
                   ORDER BY dias_retraso DESC, p.fecha_devolucion_esperada ASC";

$resultado_retrasados = $conexion->query($sql_retrasados);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Entregas Retrasadas - Biblioteca Sullivan</title>
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
        <div class="alerta <?php echo $tipoMensaje; ?>"
            style="padding: 12px; margin: 15px auto; max-width: 850px; text-align: center; border-radius: 5px; font-weight: bold; background-color: <?php echo $tipoMensaje === 'exito' ? '#d4edda' : '#f8d7da'; ?>; color: <?php echo $tipoMensaje === 'exito' ? '#155724' : '#721c24'; ?>;">
            <?php echo $mensaje; ?>
        </div>
    <?php endif; ?>

    <h2 style="text-align: center; color: #991B1B; margin-top: 30px;">⚠️ Módulo de Entregas Retrasadas</h2>
    <p style="text-align: center; color: #475569; margin-bottom: 20px;">
        Listado automático de libros vencidos (a partir de <b>1 día</b> de retraso).
    </p>

    <div class="tabla-responsiva">
        <table class="tabla-datos" style="max-width: 95%; margin: 20px auto; font-size: 13px;">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Usuario / Matrícula</th>
                    <th>Rol</th>
                    <th>Libro Retenido</th>
                    <th>Fecha Salida</th>
                    <th>Fecha Límite</th>
                    <th>Tiempo de Retraso</th>
                    <th>Acción</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($resultado_retrasados && $resultado_retrasados->num_rows > 0): ?>
                    <?php while ($fila = $resultado_retrasados->fetch_assoc()): 
                        $dias = (int) $fila['dias_retraso'];
                        $etiqueta_dias = ($dias === 1) ? "1 día de retraso" : "$dias días de retraso";
                    ?>
                        <tr style="background-color: #FFF5F5;">
                            <td style="font-weight: bold; text-align: center;"><?php echo $fila['id_prestamo']; ?></td>
                            <td>
                                <b><?php echo htmlspecialchars($fila['Persona']); ?></b>
                                <?php if (!empty($fila['MatriculaAlumno'])): ?>
                                    <br><span style="font-size: 11px; color: #0F4C81; font-weight: bold;">[<?php echo htmlspecialchars($fila['MatriculaAlumno']); ?>]</span>
                                <?php endif; ?>
                            </td>
                            <td><?php echo $fila['Rol']; ?></td>
                            <td><b><?php echo htmlspecialchars($fila['Libro']); ?></b></td>
                            <td><?php echo date('d/m/Y', strtotime($fila['fecha_salida'])); ?></td>
                            <td style="color: #991B1B; font-weight: bold;"><?php echo date('d/m/Y', strtotime($fila['fecha_devolucion_esperada'])); ?></td>
                            <td>
                                <span style="background-color: #FEE2E2; color: #991B1B; padding: 4px 10px; border-radius: 12px; font-weight: bold; font-size: 12px; display: inline-block;">
                                    ⏰ <?php echo $etiqueta_dias; ?>
                                </span>
                            </td>
                            <td>
                                <a href="retrasados.php?devolver_id=<?php echo $fila['id_prestamo']; ?>"
                                   style="text-decoration: none; padding: 6px 10px; background-color: #2A9D8F; border-radius: 4px; color: white; font-size: 11px; font-weight: bold; display: inline-block;"
                                   onclick="return confirm('¿Confirmas que este libro retrasado ya fue devuelto físicamente?');">
                                   📥 Recibir Libro
                                </a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="8" style="text-align: center; color: #065F46; font-weight: bold; padding: 25px; background-color: #F0FDF4;">
                            ✅ ¡Excelente! No hay ningún préstamo con retraso actualmente.
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <div class="z1">
        <a class="z2" href="registro.php">REGRESAR</a>
    </div>
</body>
</html>