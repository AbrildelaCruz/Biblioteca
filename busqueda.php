<?php
// 1. IMPORTAR LA CONEXIÓN AL PRINCIPIO
require_once 'conexion.php';

// Inicializar variables para los términos de búsqueda
$resultados = null;

$titulo = isset($_GET['titulo']) ? trim($_GET['titulo']) : '';
$tema   = isset($_GET['tema']) ? trim($_GET['tema']) : ''; // Este input buscará en la columna "Categoría"
$autor  = isset($_GET['autor']) ? trim($_GET['autor']) : '';
$isbn   = isset($_GET['isbn']) ? trim($_GET['isbn']) : '';

// Construimos la consulta SQL dinámica amarrada a tus columnas reales de la base de datos
if ($titulo !== '' || $tema !== '' || $autor !== '' || $isbn !== '') {
    // Consulta filtrada si el usuario llena algún campo (Filtros con LIKE)
    $query = "SELECT id_libro, Titulo, Autor, Categoría, Editorial, Edición, Idioma, Ejemplares_totales, Ejemplares_disponibles, Ejemplares_prestados FROM libros WHERE 1=1";
    
    if ($titulo !== '') { $query .= " AND Titulo LIKE '%" . $conexion->real_escape_string($titulo) . "%'"; }
    if ($tema !== '')   { $query .= " AND Categoría LIKE '%" . $conexion->real_escape_string($tema) . "%'"; }
    if ($autor !== '')  { $query .= " AND Autor LIKE '%" . $conexion->real_escape_string($autor) . "%'"; }
    if ($isbn !== '')   { $query .= " AND id_libro LIKE '%" . $conexion->real_escape_string($isbn) . "%'"; } // Ajustado ISBN a id_libro si no tienes columna física de ISBN
    
    $query .= " ORDER BY Titulo ASC";
    $resultados = $conexion->query($query);
} else {
    // Si entran por primera vez, muestra los últimos 15 libros registrados de forma automática
    $resultados = $conexion->query("SELECT id_libro, Titulo, Autor, Categoría, Editorial, Edición, Idioma, Ejemplares_totales, Ejemplares_disponibles, Ejemplares_prestados FROM libros ORDER BY id_libro DESC LIMIT 15");
}
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
     