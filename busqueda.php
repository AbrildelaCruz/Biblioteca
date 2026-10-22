<?php
require_once 'conexion.php';

$resultados = null;
$titulo = isset($_GET['titulo']) ? trim($_GET['titulo']) : '';
$tema   = isset($_GET['tema']) ? trim($_GET['tema']) : ''; 
$autor  = isset($_GET['autor']) ? trim($_GET['autor']) : '';
$isbn   = isset($_GET['isbn']) ? trim($_GET['isbn']) : '';

if ($titulo !== '' || $tema !== '' || $autor !== '' || $isbn !== '') {
    // Si hay búsqueda, usamos una consulta limpia seleccionando todo (*) para evitar errores de nombres exactos
    $query = "SELECT * FROM libros WHERE 1=1";
    
    $params = [];
    $types = "";

    if ($titulo !== '') { $query .= " AND Titulo LIKE ?"; $params[] = "%$titulo%"; $types .= "s"; }
    // Filtramos por tema buscando tanto en Categoria como en Encabezamiento_de_Materia para blindar la búsqueda
    if ($tema !== '')   { $query .= " AND (Categoria LIKE ? OR Encabezamiento_de_Materia LIKE ?)"; $params[] = "%$tema%"; $params[] = "%$tema%"; $types .= "ss"; }
    if ($autor !== '')  { $query .= " AND Autor LIKE ?"; $params[] = "%$autor%"; $types .= "s"; }
    if ($isbn !== '')   { $query .= " AND id_libro LIKE ?"; $params[] = "%$isbn%"; $types .= "s"; }
    
    $query .= " ORDER BY Titulo ASC";
    
    $stmt = $conexion->prepare($query);
    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    $resultados = $stmt->get_result();
} else {
    // Seleccionamos todo (*) para evitar el fallo de "Unknown column" si varía el acento en XAMPP
    $resultados = $conexion->query("SELECT * FROM libros ORDER BY id_libro DESC LIMIT 15");
}

/* Cargamos todos los libros de la base de datos para la lista desplegable*/
/* Usamos SELECT * para evitar fallos si varían los nombres de columna en tu XAMPP */

$query_libros = "SELECT * FROM libros ORDER BY Titulo ASC";
$resultado_libros = $conexion->query($query_libros);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Biblioteca Sullivan - Busqueda
    </title>
    <link rel="stylesheet" href="style.css?v=<?php echo time(); ?>">
    <link rel="icon" type="image/png" href="logobiblioteca.png">

</head>
<body class="body2">

    <!-- Animacion de burbujas flotantes de fondo -->
    <div class="contenedor-burbujas">
        <div class="burbuja"></div>
        <div class="burbuja"></div>
        <div class="burbuja"></div>
        <div class="burbuja"></div>
        <div class="burbuja"></div>
        <div class="burbuja"></div>
    </div>

    <!-- Bloque del Formulario de Préstamos Estilizado -->
    <div class="contenedor-formulario" style="max-width: 850px; margin-top: 30px;">
        <h2>🔍 Biblioteca Sullivan</h2>
        
            <fieldset>
                <!-- Lista Desplegable de Títulos enlazada a tu tabla 'libros' -->
                <div class="campo-grupo" style="grid-column: 1 / -1;">
                    <label for="buscar-titulo">Título del Libro:</label>
                    <select id="buscar-titulo" name="id_libro" onchange="rellenarDatosLibro()" required>
                        <option value="">-- Selecciona un libro de la lista --</option>
                        <?php if ($resultado_libros && $resultado_libros->num_rows > 0): ?>
                            <?php while ($libro = $resultado_libros->fetch_assoc()): ?>
                                <option value="<?php echo $libro['id_libro']; ?>">
                                    <?php echo htmlspecialchars($libro['Titulo']); ?>
                                </option>
                            <?php endwhile; ?>
                        <?php endif; ?>
                    </select>
                </div>

                <!-- Campos de Auto-relleno Automático (Marcados como Readonly) -->
                <div class="campo-grupo">
                    <label for="buscar-autor">Autor:</label>
                    <input type="text" id="buscar-autor" name="autor" readonly placeholder="Se rellena automáticamente...">
                </div>

                <div class="campo-grupo">
                    <label for="buscar-tema">Categoría o Tema:</label>
                    <input type="text" id="buscar-tema" name="tema" readonly placeholder="Se rellena automáticamente...">
                </div>

                <div class="campo-grupo">
                    <label for="buscar-editorial">Editorial:</label>
                    <input type="text" id="buscar-editorial" name="editorial" readonly placeholder="Se rellena automáticamente...">
                </div>

                <div class="campo-grupo">
                    <label for="buscar-disponibles">Ejemplares Disponibles:</label>
                    <input type="text" id="buscar-disponibles" name="disponibles" readonly placeholder="Se rellena automáticamente...">
                </div>
                
                <button type="submit" class="boton-guardar" style="grid-column: 1 / -1; margin-top: 15px;"> Buscar</button> 
            </fieldset>
        </form>
    </div>
           
    <!-- Botón flotante para regresar abajo a la derecha -->
    <div class="z1">
        <a class="z2" href="registro.html">REGRESAR</a>
    </div>

    <!-- Script de JavaScript para hacer la magia del auto-relleno instantáneo sin recargar -->
    <script>
    function rellenarDatosLibro() {
        var idLibro = document.getElementById('buscar-titulo').value;

        // Si el usuario vuelve a poner la opción por defecto, limpiamos los campos automáticos
        if (idLibro == "") {
            document.getElementById('buscar-autor').value = "";
            document.getElementById('buscar-tema').value = "";
            document.getElementById('buscar-editorial').value = "";
            document.getElementById('buscar-disponibles').value = "";
            return;
        }

        // Hacemos una consulta invisible al archivo 'buscar_libro.php' pasando el ID del libro
        fetch('buscar_libro.php?id=' + idLibro)
            .then(response => response.json())
            .then(data => {
                if (!data.error) {
                    // Mapeamos los datos de tu tabla 'libros' directamente a las casillas del formulario
                    document.getElementById('buscar-autor').value = data.Autor ?? 'No especificado';
                    
                    // Validamos si viene con o sin acento desde tu base de datos
                    document.getElementById('buscar-tema').value = data.Categoria ?? data.Categoría ?? 'Sin categoría';
                    document.getElementById('buscar-editorial').value = data.Editorial ?? 'No especificada';
                    
                    // Colocamos el stock disponible en formato entendible
                    var disponibles = data.Ejemplares_disponibles ?? 0;
                    var totales = data.Ejemplares_totales ?? 0;
                    document.getElementById('buscar-disponibles').value = disponibles + " de " + totales;
                } else {
                    alert("Error: No se pudieron obtener los datos de este libro.");
                }
            })
            .catch(error => {
                console.error('Error en la petición Fetch:', error);
            });
    }
    </script>

</body>
</html>