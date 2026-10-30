<?php
require_once 'conexion.php';

// 1. RECUPERAR MENSAJES DE LA SESIÓN
$mensaje = $_SESSION['mensaje'] ?? '';
$tipoMensaje = $_SESSION['tipoMensaje'] ?? '';
unset($_SESSION['mensaje'], $_SESSION['tipoMensaje']);

// Variables para el modo edición con tus nuevos campos bibliográficos
$modo_editar = false;
$id_libro = '';
$titulo = '';
$autor = '';
$encabezamiento_de_materia = '';
$editorial = '';
$isbn = '';
$ano = '';
$clasificacion = '';
$pais = '';
$idioma = '';
$descripcion = '';
$estado_conservacion = 'Excelente';
$ejemplares_totales = 1;

// 2. DETECTAR SI SE SOLICITA EDITAR UN REGISTRO (MÉTODO GET)
if (isset($_GET['editar_id'])) {
    $id_editar = filter_input(INPUT_GET, 'editar_id', FILTER_VALIDATE_INT);
    if ($id_editar) {
        $stmt_buscar = $conexion->prepare('SELECT * FROM libros WHERE id_libro = ?');
        $stmt_buscar->bind_param('i', $id_editar);
        $stmt_buscar->execute();
        $resultado_buscar = $stmt_buscar->get_result();
        if ($libro_data = $resultado_buscar->fetch_assoc()) {
            $modo_editar = true;
            $id_libro = $libro_data['id_libro'];
            $titulo = $libro_data['Titulo'];
            $autor = $libro_data['Autor'];
            $encabezamiento_de_materia = $libro_data['Encabezamiento_de_Materia'] ?? '';
            $editorial = $libro_data['Editorial'];
            $isbn = $libro_data['ISBN'];
            $ano = $libro_data['ano'] ?? '';
            $clasificacion = $libro_data['clasificacion'] ?? '';

            // 🔄 SOLUCCIÓN: Forzamos la comprobación exacta de 'Pais' cuidando mayúsculas y minúsculas
            $pais = $libro_data['Pais'] ?? $libro_data['pais'] ?? '';

            $idioma = $libro_data['Idioma'];
            $descripcion = $libro_data['descripcion'] ?? '';
            $estado_conservacion = $libro_data['Estado_conservación'] ?? $libro_data['Estado_conservacion'] ?? 'Excelente';
            $ejemplares_totales = $libro_data['Ejemplares_totales'];
        }
        $stmt_buscar->close();
    }
}

// 3. PROCESAR EL FORMULARIO (REGISTRO O ACTUALIZACIÓN - MÉTODO POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo    = trim($_POST['Titulo'] ?? '');
    $autor     = trim($_POST['Autor'] ?? '');
    $categoria = trim($_POST['Categoria'] ?? '');
    $editorial = trim($_POST['Editorial'] ?? '');
    $edicion   = trim($_POST['Edicion'] ?? '');
    $isbn      = trim($_POST['ISBN'] ?? ''); // O 'ISBM' según tu columna real de la BD
    $pais      = trim($_POST['Pais'] ?? '');
    $idioma    = trim($_POST['Idioma'] ?? '');
    $conservacion = trim($_POST['Estado_conservacion'] ?? 'Excelente');
    $ejemplares = intval($_POST['Ejemplares_totales'] ?? 1); // Cantidad de libros físicos que entran

    if ($titulo === '' || $autor === '' || $ejemplares < 1) {
        $_SESSION['mensaje'] = 'Título, Autor y Ejemplares Totales son campos obligatorios.';
        $_SESSION['tipoMensaje'] = 'error';
    } else {
        // 🚀 LA MAGIA DE LA INGENIERÍA:
        // Mandamos a guardar los $ejemplares en Totales y en Disponibles, y ponemos 0 en Prestados.
        $stmt = $conexion->prepare("INSERT INTO libros (Titulo, Autor, Categoria, Editorial, Edicion, ISBN, Pais, Idioma, Ejemplares_totales, Ejemplares_disponibles, Ejemplares_prestados, Estado_conservacion) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 0, ?)");
        $stmt->bind_param("ssssssssiis", $titulo, $autor, $categoria, $editorial, $edicion, $isbn, $pais, $idioma, $ejemplares, $ejemplares, $conservacion);

        if ($stmt && $stmt->execute()) {
            $_SESSION['mensaje'] = "¡Libro registrado exitosamente en el catálogo e inventario!";
            $_SESSION['tipoMensaje'] = 'exito';
            $stmt->close();
        } else {
            $_SESSION['mensaje'] = 'Error al registrar en la base de datos: ' . $conexion->error;
            $_SESSION['tipoMensaje'] = 'error';
        }
    }
    // Redirección PRG para que el F5 no te duplique el libro
    header("Location: ver_catalogo.php"); 
    exit();
}
// 4. PROCESAR EL BORRADO EN EL CATÁLOGO (MÉTODO GET)
if (isset($_GET['eliminar_id'])) {
    $id_a_borrar = filter_input(INPUT_GET, 'eliminar_id', FILTER_VALIDATE_INT);
    if ($id_a_borrar) {
        $stmt_borrar = $conexion->prepare('DELETE FROM libros WHERE id_libro = ?');
        if ($stmt_borrar) {
            $stmt_borrar->bind_param('i', $id_a_borrar);
            if ($stmt_borrar->execute()) {
                $_SESSION['mensaje'] = 'Libro eliminado del catálogo correctamente.';
                $_SESSION['tipoMensaje'] = 'exito';
            } else {
                $_SESSION['mensaje'] = 'Error al eliminar del catálogo.';
                $_SESSION['tipoMensaje'] = 'error';
            }
            $stmt_borrar->close();
        } else {
            $_SESSION['mensaje'] = 'Error al preparar la consulta.';
            $_SESSION['tipoMensaje'] = 'error';
        }
    }
    header("Location: ver_catalogo.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> Registro - Libros</title>
    <link rel="stylesheet" href="style.css?v=<?php echo time(); ?>">
    <link rel="icon" type="image/png" href="logobiblioteca.png">
</head>

<body class="body2">

    <div class="contenedor-burbujas">
        <div class="burbuja"></div>
        <div class="burbuja"></div>
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

    <div class="contenedor-formulario" style="max-width: 800px;">
        <h2><?php echo $modo_editar ? '📝 Modificar Libro del Catálogo' : '📚 Registro de Libros'; ?></h2>

        <form method="post" action="catalogo.php" id="formLibro">
            <input type="hidden" name="id_libro" value="<?php echo $id_libro; ?>">

            <!-- Bloque de campos superiores en CSS Grid -->
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">

                <!-- Título de Libro incluye el botón Buscar al lado derecho -->
                <div class="grupo-campo">
                    <label>Título del Libro</label>
                    <div style="display: flex; gap: 8px;">
                        <input type="text" id="input_Titulo" name="Titulo"
                            value="<?php echo htmlspecialchars($titulo); ?>" maxlength="255" style="flex: 1;">
                       
                    </div>
                </div>

                <div class="grupo-campo">
                    <label>Autor</label>
                    <input type="text" id="input_Autor" name="Autor" value="<?php echo htmlspecialchars($autor); ?>"
                        maxlength="255">
                </div>

                <div class="grupo-campo">
                    <label>Encabezamiento de Materia</label>
                    <input type="text" id="input_Materia" name="Encabezamiento_de_Materia"
                        value="<?php echo htmlspecialchars($encabezamiento_de_materia); ?>" maxlength="255">
                </div>

                <div class="grupo-campo">
                    <label>Editorial</label>
                    <input type="text" id="input_Editorial" name="Editorial"
                        value="<?php echo htmlspecialchars($editorial); ?>" maxlength="100">
                </div>

                <div class="grupo-campo">
                    <label>ISBN / Código</label>
                    <input type="text" id="input_ISBN" name="ISBN" value="<?php echo htmlspecialchars($isbn); ?>"
                        maxlength="50" style="width: 100%; box-sizing: border-box;">
                </div>

                <div class="grupo-campo">
                    <label>Año</label>
                    <input type="number" id="input_ano" name="ano" value="<?php echo htmlspecialchars($ano); ?>"
                        max="9999">
                </div>

                <div class="grupo-campo">
                    <label>Clasificación</label>
                    <input type="text" id="input_clasificacion" name="clasificacion"
                        value="<?php echo htmlspecialchars($clasificacion); ?>" maxlength="100">
                </div>

                <div class="grupo-campo">
                    <label>País</label>
                    <input type="text" id="input_Pais" name="Pais" value="<?php echo htmlspecialchars($pais); ?>"
                        maxlength="50">
                </div>

                <!-- 🛠️ CORRECCIÓN CSS: Forzamos a que ocupen las 2 columnas del Grid para que no se oculten -->

                <!-- IDIOMA -->
                <div class="grupo-campo" style="grid-column: span 2; width: 100%;">
                    <label style="display: block; margin-bottom: 8px; font-weight: bold;">Idioma</label>
                    <input type="text" id="input_Idioma" name="Idioma" value="<?php echo htmlspecialchars($idioma); ?>"
                        maxlength="50" style="width: 100%; box-sizing: border-box;">
                </div>

                <!-- DESCRIPCIÓN -->
                <div class="grupo-campo" style="grid-column: span 2; width: 100%;">
                    <label style="display: block; margin-bottom: 8px; font-weight: bold;">Descripción / Notas</label>
                    <input type="text" id="input_descripcion" name="descripcion"
                        value="<?php echo htmlspecialchars($descripcion); ?>" maxlength="255"
                        style="width: 100%; box-sizing: border-box;">
                </div>

                <!-- EJEMPLARES TOTALES -->
                <div class="grupo-campo" style="grid-column: span 2; margin-bottom: 10px; width: 100%;">
                    <label style="display: block; margin-bottom: 8px; font-weight: bold;">Ejemplares Totales</label>
                    <input type="number" id="input_Ejemplares" name="Ejemplares_totales"
                        value="<?php echo htmlspecialchars($ejemplares_totales); ?>" min="1"
                        style="width: 100%; box-sizing: border-box;">
                </div>

                <!-- BOTÓN GUARDAR -->
                <div class="grupo-campo" style="grid-column: span 2; width: 100%;">
                    <button type="submit" class="boton-guardar"
                        style="width: 100%; padding: 14px; font-size: 15px; font-weight: bold; cursor: pointer; box-sizing: border-box;">
                        <?php echo $modo_editar ? 'Actualizar Libro e Inventario' : 'REGISTRAR LIBRO'; ?>
                    </button>
                </div>

                <div style="grid-column: span 2; margin-top: 5px; width: 100%;">
                    <a href="ver_catalogo.php"
                        style="display: block; text-align: center; font-weight: bold; color: white; text-decoration: none; background-color: #28a745; padding: 12px; border-radius: 5px; box-sizing: border-box;">🔍
                        Ver Todos los Libros Registrados</a>
                </div>

            </div>
        </form>
    </div>

    <div class="z1">
        <a class="z2" href="registro.php">REGRESAR</a>
    </div>

    <script src="main.js"></script>

    <!-- 🚀 LÓGICA DE BÚSQUEDA AUTOMÁTICA COMPLETA -->
    <script>
        document.getElementById('btnBuscarLibro').addEventListener('click', function () {
            const datosBusqueda = {
                Titulo: document.getElementById('input_Titulo').value,
                Autor: document.getElementById('input_Autor').value,
                Encabezamiento_de_Materia: document.getElementById('input_Materia').value,
                Editorial: document.getElementById('input_Editorial').value,
                ISBN: document.getElementById('input_ISBN').value,
                ano: document.getElementById('input_ano').value,
                clasificacion: document.getElementById('input_clasificacion').value,
                descripcion: document.getElementById('input_descripcion').value,
                Pais: document.getElementById('input_Pais').value,
                Idioma: document.getElementById('input_Idioma').value
            };

            const tieneCriterio = Object.values(datosBusqueda).some(valor => valor.trim() !== "");

            if (!tieneCriterio) {
                alert("Por favor, rellena al menos un campo para realizar la búsqueda.");
                return;
            }

            fetch('buscar_libro.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(datosBusqueda)
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.libro) {
                        // Rellenar dinámicamente mapeando las columnas de la BD
                        document.getElementById('input_Titulo').value = data.libro.Titulo || '';
                        document.getElementById('input_Autor').value = data.libro.Autor || '';
                        document.getElementById('input_Materia').value = data.libro.Encabezamiento_de_Materia || '';
                        document.getElementById('input_Editorial').value = data.libro.Editorial || '';
                        document.getElementById('input_ISBN').value = data.libro.ISBN || '';
                        document.getElementById('input_ano').value = data.libro.ano || '';
                        document.getElementById('input_clasificacion').value = data.libro.clasificacion || '';

                        // 🔄 ASIGNACIÓN MÁGICA: Mapea 'Pais' (con P mayúscula de tu tabla) al input
                        document.getElementById('input_Pais').value = data.libro.Pais || data.libro.pais || '';

                        document.getElementById('input_Idioma').value = data.libro.Idioma || '';
                        document.getElementById('input_descripcion').value = data.libro.descripcion || '';
                        document.getElementById('input_Ejemplares').value = data.libro.Ejemplares_totales || '1';
                    } else {
                        alert('No se encontró ningún libro que coincida con los datos ingresados.');
                    }
                })
                .catch(error => {
                    console.error('Error en la búsqueda:', error);
                    alert('Ocurrió un error al procesar la búsqueda en el servidor.');
                });
        });
    </script>
</body>

</html>