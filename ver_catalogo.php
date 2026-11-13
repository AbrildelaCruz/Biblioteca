<?php
require_once 'conexion.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$mensaje = $_SESSION['mensaje'] ?? '';
$tipoMensaje = $_SESSION['tipoMensaje'] ?? '';
unset($_SESSION['mensaje'], $_SESSION['tipoMensaje']);

$categorias_disponibles = [
    'Novela y Narrativa',
    'Cuento y Relato',
    'Poesía',
    'Teatro y Dramaturgia',
    'Historia y Biografía',
    'Filosofía y Ciencia',
    'Aventuras y Juvenil'
];

// CONFIGURACIÓN DEL PAGINADOR
$registros_por_pagina = 15;
$pagina_actual = isset($_GET['pagina']) ? filter_input(INPUT_GET, 'pagina', FILTER_VALIDATE_INT) : 1;
if (!$pagina_actual || $pagina_actual < 1) {
    $pagina_actual = 1;
}
$desplazamiento = ($pagina_actual - 1) * $registros_por_pagina;

$sql_total = "SELECT COUNT(*) AS total FROM libros";
$total_registros = $conexion->query($sql_total)->fetch_assoc()['total'];
$total_paginas = ceil($total_registros / $registros_por_pagina);

$sql_datos = "SELECT * FROM libros ORDER BY id_libro ASC LIMIT ? OFFSET ?";
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
    <title>Libros Registrados - Biblioteca Sullivan</title>
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
            style="padding: 10px; margin: 15px auto; max-width: 850px; text-align: center; border-radius: 5px; font-weight: bold; background-color: <?php echo $tipoMensaje === 'exito' ? '#d4edda' : '#f8d7da'; ?>; color: <?php echo $tipoMensaje === 'exito' ? '#155724' : '#721c24'; ?>;">
            <?php echo $mensaje; ?>
        </div>
    <?php endif; ?>

    <h2 style="text-align: center; color: #0F4C81; margin-top: 30px;">📚 Libros Registrados en la Base de Datos</h2>

    <!-- BUSCADOR GLOBAL + FILTRO POR CATEGORÍA -->
    <div class="contenedor-busqueda" style="margin: 20px auto; max-width: 850px; display: flex; flex-wrap: wrap; gap: 10px; justify-content: center; padding: 0 15px;">
        <input type="text" id="buscar-tabla-libros" class="input-busqueda" style="flex: 2; min-width: 240px;"
               placeholder="Buscar libro por título, autor, editorial o ISBN..." onkeyup="buscarGlobalLibros()">
        
        <select id="filtro-categoria-libros" class="input-busqueda" style="flex: 1; min-width: 200px; cursor: pointer;" onchange="buscarGlobalLibros()">
            <option value="">📂 Todas las Categorías</option>
            <?php foreach ($categorias_disponibles as $cat): ?>
                <option value="<?php echo $cat; ?>"><?php echo $cat; ?></option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="tabla-responsiva">
        <table class="tabla-datos" style="max-width: 95%; margin: 20px auto; font-size: 13px;">
            <thead>
                <tr>
                    <th>N°</th>
                    <th>Título-libro</th>
                    <th>Autor</th>
                    <th>Categoría</th>
                    <th>Editorial</th>
                    <th>ISBN</th>
                    <th>Año</th>
                    <th>Clasificación</th>
                    <th>Ej. Totales</th>
                    <th>Ej. Disponibles</th>
                    <th>Ej. Prestados</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody id="tabla-cuerpo-libros">
                <?php if ($resultado && $resultado->num_rows > 0):
                    $num = $desplazamiento + 1;
                    while ($fila = $resultado->fetch_assoc()): 
                        $clasif = $fila['Clasificación'] ?? $fila['clasificacion'] ?? '-';
                        ?>
                        <tr>
                            <td style="font-weight: bold; text-align: center;"><?php echo $num; ?></td>
                            <td><b><?php echo htmlspecialchars($fila['Titulo']); ?></b></td>
                            <td><?php echo htmlspecialchars($fila['Autor']); ?></td>
                            <td><span style="padding: 3px 7px; background: #EFF6FF; color: #1E40AF; border-radius: 12px; font-size: 11px; font-weight: bold;"><?php echo htmlspecialchars($fila['Categoria'] ?? 'Novela y Narrativa'); ?></span></td>
                            <td><?php echo htmlspecialchars($fila['Editorial']); ?></td>
                            <td><?php echo htmlspecialchars($fila['ISBN'] ?? '-'); ?></td>
                            <td><?php echo htmlspecialchars($fila['ano'] ?? '-'); ?></td>
                            <td><?php echo htmlspecialchars($clasif); ?></td>
                            <td style="text-align: center;"><?php echo $fila['Ejemplares_totales'] ?? 0; ?></td>
                            <td style="text-align: center; color: green; font-weight: bold;"><?php echo $fila['Ejemplares_disponibles'] ?? 0; ?></td>
                            <td style="text-align: center; color: red;"><?php echo $fila['Ejemplares_prestados'] ?? 0; ?></td>
                            <td>
                                <a href="catalogo.php?editar_id=<?php echo $fila['id_libro']; ?>"
                                    style="text-decoration: none; padding: 3px 6px; background-color: #e2e3e5; border-radius: 3px; color: #383d41; font-size: 11px;">Editar</a>
                                <a href="catalogo.php?eliminar_id=<?php echo $fila['id_libro']; ?>"
                                    style="text-decoration: none; padding: 3px 6px; background-color: #f8d7da; border-radius: 3px; color: #721c24; font-size: 11px; margin-left: 2px;"
                                    onclick="return confirm('¿Seguro que deseas eliminar este libro?');">Eliminar</a>
                            </td>
                        </tr>
                        <?php
                        $num++;
                    endwhile;
                else: ?>
                    <tr>
                        <td colspan="12" style="text-align: center;">No hay libros registrados en el catálogo.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <?php if ($total_paginas > 1): ?>
        <div class="paginador" id="paginador-libros">
            <a href="?pagina=<?php echo $pagina_actual - 1; ?>"
                class="<?php echo ($pagina_actual <= 1) ? 'deshabilitado' : ''; ?>">« Anterior</a>

            <?php for ($i = 1; $i <= $total_paginas; $i++): ?>
                <a href="?pagina=<?php echo $i; ?>" class="<?php echo ($pagina_actual == $i) ? 'activa' : ''; ?>">
                    <?php echo $i; ?>
                </a>
            <?php endfor; ?>

            <a href="?pagina=<?php echo $pagina_actual + 1; ?>"
                class="<?php echo ($pagina_actual >= $total_paginas) ? 'deshabilitado' : ''; ?>">Siguiente »</a>
        </div>
    <?php endif; ?>

    <div style="text-align: center; margin-bottom: 40px;">
        <a href="catalogo.php"
            style="display: inline-block; padding: 10px 20px; background-color: #007bff; color: white; text-decoration: none; border-radius: 5px; font-weight: bold;">➕
            Registrar Nuevo Libro</a>
    </div>

    <div class="z1">
        <a class="z2" href="registro.php">REGRESAR</a>
    </div>

    <script>
    function buscarGlobalLibros() {
        const query = document.getElementById('buscar-tabla-libros').value;
        const categoria = document.getElementById('filtro-categoria-libros').value;
        const paginador = document.getElementById('paginador-libros');
        const cuerpoTabla = document.getElementById('tabla-cuerpo-libros');

        if (paginador) {
            paginador.style.display = (query.trim() !== '' || categoria !== '') ? 'none' : 'block';
        }

        const url = 'buscar_libros_global.php?query=' + encodeURIComponent(query) + '&categoria=' + encodeURIComponent(categoria);

        fetch(url)
            .then(response => response.text())
            .then(htmlFilas => {
                cuerpoTabla.innerHTML = htmlFilas;
            })
            .catch(error => console.error('Error en la búsqueda global de libros:', error));
    }
    </script>
</body>
</html>