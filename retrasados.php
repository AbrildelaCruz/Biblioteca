<?php
require_once 'conexion.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Consulta SQL: Busca solo alumnos que ya pasaron los 7 días de préstamo y aún no devuelven el libro
$sql_retrasados = "SELECT 
                        p.id_prestamo,
                        a.Nombre AS Alumna,
                        a.Grupo,
                        a.Familia,
                        l.Titulo AS Libro,
                        p.fecha_salida,
                        DATEDIFF(CURDATE(), DATE_ADD(p.fecha_salida, INTERVAL 7 DAY)) AS dias_retraso
                   FROM prestamos p
                   INNER JOIN alumnos a ON p.id_Alumnos = a.id_Alumnos
                   INNER JOIN libros l ON p.id_libro = l.id_libro
                   WHERE p.fecha_devolucion_real IS NULL 
                     AND DATEDIFF(CURDATE(), DATE_ADD(p.fecha_salida, INTERVAL 7 DAY)) > 0
                   ORDER BY dias_retraso DESC";

$resultado = $conexion->query($sql_retrasados);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Adeudos y Retrasos - Biblioteca Sullivan</title>
    <link rel="stylesheet" href="style.css?v=<?php echo time(); ?>">
    <link rel="icon" type="image/png" href="logobiblioteca.png">
</head>
<body class="body2">
    <div class="contenedor-burbujas">
        <div class="burbuja"></div>
        <div class="burbuja"></div>
        <div class="burbuja"></div>
    </div>

    <h2 style="text-align: center; color: #991B1B; margin-top: 30px; text-transform: uppercase;">
        ⚠️ Alumnas con Entrega Retrasada
    </h2>
    <p style="text-align: center; color: #475569; margin-bottom: 20px;">
        Lista automática de libros que superaron el plazo límite de 7 días.
    </p>

    <table class="tabla-datos" style="max-width: 95%; margin: 20px auto; font-size: 14px;">
        <thead>
            <tr>
                <th>Folio</th>
                <th>Nombre de la Alumna</th>
                <th>Grupo</th>
                <th>Familia</th>
                <th>Libro Retenido</th>
                <th>Fecha Salida</th>
                <th>Días de Retraso</th>
                <th>Acción</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($resultado && $resultado->num_rows > 0): ?>
                <?php while ($fila = $resultado->fetch_assoc()): ?>
                    <tr>
                        <td style="font-weight: bold; text-align: center;"><?php echo $fila['id_prestamo']; ?></td>
                        <td><b><?php echo htmlspecialchars($fila['Alumna']); ?></b></td>
                        <td><?php echo htmlspecialchars($fila['Grupo'] ?? '-'); ?></td>
                        <td><?php echo htmlspecialchars($fila['Familia'] ?? '-'); ?></td>
                        <td><i><?php echo htmlspecialchars($fila['Libro']); ?></i></td>
                        <td><?php echo date('d/m/Y', strtotime($fila['fecha_salida'])); ?></td>
                        <td style="text-align: center;">
                            <span style="background-color: #FEE2E2; color: #991B1B; padding: 4px 10px; border-radius: 12px; font-weight: bold; font-size: 12px;">
                                🚨 <?php echo $fila['dias_retraso']; ?> días
                            </span>
                        </td>
                        <td>
                            <a href="prestamos.php?devolver_id=<?php echo $fila['id_prestamo']; ?>" 
                               style="text-decoration: none; padding: 5px 10px; background-color: #2A9D8F; border-radius: 4px; color: white; font-size: 12px; font-weight: bold; display: inline-block;"
                               onclick="return confirm('¿Confirmas que la alumna ya entregó el libro físicamente?');">
                               📥 Recibir
                            </a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="8" style="text-align: center; font-weight: bold; color: #065F46; padding: 25px;">
                        ✅ ¡Excelente! Ninguna alumna tiene libros retrasados actualmente.
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <div class="z1">
        <a class="z2" href="registro.php">REGRESAR</a>
    </div>
</body>
</html>