<?php
require_once 'conexion.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$mensaje = $_SESSION['mensaje'] ?? '';
$tipoMensaje = $_SESSION['tipoMensaje'] ?? '';
unset($_SESSION['mensaje'], $_SESSION['tipoMensaje']);

// 1. CAPTURAR EL TÉRMINO DE BÚSQUEDA
$busqueda = isset($_GET['buscar']) ? trim($_GET['buscar']) : '';

$registros_por_pagina = 10; // Cantidad de profesores por página
$pagina_actual = isset($_GET['pagina']) ? filter_input(INPUT_GET, 'pagina', FILTER_VALIDATE_INT) : null;

// ==========================================
//   NUEVO: REDIRECCIÓN AUTOMÁTICA A LA PÁGINA DEL PROFESOR
// ==========================================
if ($busqueda !== '' && $pagina_actual === null) {
    try {
        // Obtenemos todos los profesores ordenados tal cual se muestran en la tabla
        $sql_posicion = "SELECT id_profesores, Nombre FROM profesores ORDER BY id_profesores ASC";
        $res_posicion = $conexion->query($sql_posicion);

        $indice_registro = -1;
        $contador = 0;

        while ($fila_pos = $res_posicion->fetch_assoc()) {
            // Buscamos coincidencia del nombre (insensible a mayúsculas/minúsculas)
            if (mb_stripos($fila_pos['Nombre'], $busqueda) !== false) {
                $indice_registro = $contador;
                break; // Detener en el primer profesor que coincida
            }
            $contador++;
        }

        // Si encontramos al profesor, calculamos matemáticamente su página exacta
        if ($indice_registro != -1) {
            $pagina_calculada = floor($indice_registro / $registros_por_pagina) + 1;

            // Redirigimos al usuario a esa página específica manteniendo la búsqueda viva
            header("Location: ver_profesores.php?pagina=" . $pagina_calculada . "&buscar=" . urlencode($busqueda));
            exit();
        }
    } catch (Exception $e) {
        // Si hay un error, continúa el flujo normal
    }
}

// Si no se buscó nada o ya se calculó la página, definir valor por defecto
if (!$pagina_actual || $pagina_actual < 1) {
    $pagina_actual = 1;
}

// Calcular desde qué registro leer (OFFSET)
$desplazamiento = ($pagina_actual - 1) * $registros_por_pagina;

// Contar el total de profesores existentes para el paginador
$sql_total = "SELECT COUNT(*) AS total FROM profesores";
$resultado_total = $conexion->query($sql_total);
$fila_total = $resultado_total->fetch_assoc();
$total_registros = $fila_total['total'];

// Calcular el número total de páginas
$total_paginas = ceil($total_registros / $registros_por_pagina);

// 2. CONSULTAR REGISTROS LIMITADOS CORRESPONDIENTES A LA PÁGINA ACTIVA
$sql_datos = "SELECT * FROM profesores ORDER BY id_profesores ASC LIMIT ? OFFSET ?";
$stmt = $conexion->prepare($sql_datos);
$stmt->bind_param("ii", $registros_por_pagina, $desplazamiento);
$stmt->execute();
$resultado = $stmt->get_result();
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
        <div class="alerta"
            style="padding: 12px; margin: 20px auto; max-width: 700px; text-align: center; border-radius: 8px; font-weight: bold; box-shadow: 0 4px 12px rgba(0,0,0,0.05); background-color: <?php echo $tipoMensaje === 'exito' ? '#E8F5E9' : '#FFEBEE'; ?>; color: <?php echo $tipoMensaje === 'exito' ? '#2E7D32' : '#C62828'; ?>; border: 1px solid <?php echo $tipoMensaje === 'exito' ? '#C8E6C9' : '#FFCDD2'; ?>;">
            <?php echo $mensaje; ?>
        </div>
    <?php endif; ?>

    <h2
        style="text-align: center; color: white; margin-top: 30px; font-family: 'Segoe UI', Arial, sans-serif; text-shadow: 0 2px 4px rgba(0,0,0,0.15);">
        👨‍🏫 Panel de Control - Profesores de la Institución</h2>

    <table class="tabla-datos" style="max-width: 85%; margin: 30px auto; font-size: 13px;">
        <thead>
            <tr>
                <th>N°</th>
                <th>Nombre del Profesor</th>
                <th>Materia(s) Asignada(s)</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($resultado && $resultado->num_rows > 0):
                $num = $desplazamiento + 1;

                while ($fila = $resultado->fetch_assoc()):
                    $id_actual = $fila['id_profesores'];
                    $materias_docente = $fila['Materia'] ?? $fila['materia'] ?? '-';
                    $nombre_docente = $fila['Nombre'] ?? $fila['nombre'] ?? '';

                    // Comprobamos si el nombre contiene la palabra buscada
                    $clase_resaltado = '';
                    if ($busqueda !== '' && mb_stripos($nombre_docente, $busqueda) !== false) {
                        $clase_resaltado = 'fila-resaltada';
                    }
                    ?>
                    <tr class="<?php echo $clase_resaltado; ?>">
                        <td style="font-weight: bold; text-align: center;"><?php echo $num; ?></td>
                        <td><b><?php echo htmlspecialchars($nombre_docente); ?></b></td>
                        <td><span
                                style="color: #0F4C81; font-weight: 500;"><?php echo htmlspecialchars($materias_docente); ?></span>
                        </td>
                        <td>
                            <a href="profesores.php?editar_id=<?php echo $id_actual; ?>"
                                style="text-decoration: none; padding: 4px 8px; background-color: #007bff; border-radius: 4px; color: white; font-size: 11px; margin-right: 5px; font-weight: bold;">Editar</a>
                            <a href="profesores.php?eliminar_id=<?php echo $id_actual; ?>"
                                style="text-decoration: none; padding: 4px 8px; background-color: #f8d7da; border-radius: 4px; color: #721c24; font-size: 11px; font-weight: bold;"
                                onclick="return confirm('¿Seguro que deseas eliminar este profesor?');">Eliminar</a>
                        </td>
                    </tr>
                    <?php
                    $num++;
                    ?>
                <?php endwhile;
            else: ?>
                <tr>
                    <td colspan="4" style="text-align: center;">No hay profesores registrados actualmente.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <!-- ==========================================
          RENDERIZADO VISUAL DEL PAGINADOR
    ========================================== -->
    <?php if ($total_paginas > 1): ?>
        <div class="paginador">
            <?php $query_buscar = ($busqueda !== '') ? '&buscar=' . urlencode($busqueda) : ''; ?>

            <a href="?pagina=<?php echo $pagina_actual - 1; ?><?php echo $query_buscar; ?>"
                class="<?php echo ($pagina_actual <= 1) ? 'deshabilitado' : ''; ?>">« Anterior</a>

            <?php for ($i = 1; $i <= $total_paginas; $i++): ?>
                <a href="?pagina=<?php echo $i; ?><?php echo $query_buscar; ?>"
                    class="<?php echo ($pagina_actual == $i) ? 'activa' : ''; ?>">
                    <?php echo $i; ?>
                </a>
            <?php endfor; ?>

            <a href="?pagina=<?php echo $pagina_actual + 1; ?><?php echo $query_buscar; ?>"
                class="<?php echo ($pagina_actual >= $total_paginas) ? 'deshabilitado' : ''; ?>">Siguiente »</a>
        </div>
    <?php endif; ?>

    <div style="text-align: center; margin-bottom: 40px;">
        <a href="profesores.php"
            style="display: inline-block; padding: 12px 24px; background-color: #2A9D8F; color: white; text-decoration: none; border-radius: 6px; font-weight: bold; transition: background-color 0.2s;"
            onmouseover="this.style.backgroundColor='#218074'" onmouseout="this.style.backgroundColor='#2A9D8F'">➕
            Registrar Nuevo Profesor</a>
    </div>

    <div class="z1">
        <a class="z2" href="registro.php">REGRESAR</a>
    </div>
</body>

</html>