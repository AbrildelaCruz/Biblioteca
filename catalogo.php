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

$modo_editar = false;
$id_libro = '';
$titulo = '';
$autor = '';
$categoria = 'Novela y Narrativa';
$encabezamiento_de_materia = '';
$editorial = '';
$isbn = '';
$ano = '';
$clasificacion = '';
$pais = '';
$idioma = 'Español';
$descripcion = '';
$ejemplares_totales = 1;

// 1. DETECTAR MODO EDICIÓN
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
            $categoria = $libro_data['Categoria'] ?? 'Novela y Narrativa';
            $encabezamiento_de_materia = $libro_data['Encabezamiento de Materia'] ?? '';
            $editorial = $libro_data['Editorial'];
            $isbn = $libro_data['ISBN'];
            $ano = $libro_data['ano'] ?? '';
            $clasificacion = $libro_data['Clasificación'] ?? $libro_data['clasificacion'] ?? '';
            $pais = $libro_data['País'] ?? $libro_data['Pais'] ?? '';
            $idioma = $libro_data['Idioma'] ?? 'Español';
            $descripcion = $libro_data['Descripción'] ?? $libro_data['descripcion'] ?? '';
            $ejemplares_totales = $libro_data['Ejemplares_totales'] ?? 1;
        }
        $stmt_buscar->close();
    }
}

// 2. PROCESAR REGISTRO O ACTUALIZACIÓN (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_libro_post = filter_input(INPUT_POST, 'id_libro', FILTER_VALIDATE_INT);
    $titulo = trim($_POST['Titulo'] ?? '');
    $autor = trim($_POST['Autor'] ?? '');
    $categoria = trim($_POST['Categoria'] ?? 'Novela y Narrativa');
    $encabezamiento = trim($_POST['Encabezamiento_de_Materia'] ?? '');
    $editorial = trim($_POST['Editorial'] ?? '');
    $isbn = trim($_POST['ISBN'] ?? '');
    $ano = intval($_POST['ano'] ?? 0);
    $clasificacion = trim($_POST['clasificacion'] ?? '');
    $pais = trim($_POST['Pais'] ?? '');
    $idioma = trim($_POST['Idioma'] ?? 'Español');
    $descripcion = trim($_POST['descripcion'] ?? '');
    $nuevos_totales = intval($_POST['Ejemplares_totales'] ?? 1);

    if ($titulo === '' || $autor === '' || $nuevos_totales < 1) {
        $_SESSION['mensaje'] = '⚠️ Título, Autor y Ejemplares Totales (mínimo 1) son obligatorios.';
        $_SESSION['tipoMensaje'] = 'error';
    } else {
        if ($id_libro_post) {
            // MODO ACTUALIZAR: Consultamos cuántos están prestados actualmente para no perder la cuenta
            $stmt_act = $conexion->prepare("SELECT Ejemplares_prestados FROM libros WHERE id_libro = ?");
            $stmt_act->bind_param("i", $id_libro_post);
            $stmt_act->execute();
            $datos_act = $stmt_act->get_result()->fetch_assoc();
            $stmt_act->close();

            $prestados_actuales = intval($datos_act['Ejemplares_prestados'] ?? 0);

            if ($nuevos_totales < $prestados_actuales) {
                $_SESSION['mensaje'] = "❌ Error: No puedes poner $nuevos_totales ejemplares totales porque actualmente hay $prestados_actuales prestados.";
                $_SESSION['tipoMensaje'] = 'error';
            } else {
                $nuevos_disponibles = $nuevos_totales - $prestados_actuales;

                $sql_upd = "UPDATE libros SET 
                            Titulo = ?, Autor = ?, Categoria = ?, Editorial = ?, ISBN = ?, 
                            ano = ?, `País` = ?, Idioma = ?, `Descripción` = ?, 
                            `Encabezamiento de Materia` = ?, `Clasificación` = ?, 
                            Ejemplares_totales = ?, Ejemplares_disponibles = ? 
                            WHERE id_libro = ?";
                $stmt = $conexion->prepare($sql_upd);
                $stmt->bind_param(
                    "sssssisssssiii",
                    $titulo, $autor, $categoria, $editorial, $isbn,
                    $ano, $pais, $idioma, $descripcion,
                    $encabezamiento, $clasificacion,
                    $nuevos_totales, $nuevos_disponibles, $id_libro_post
                );

                if ($stmt && $stmt->execute()) {
                    $_SESSION['mensaje'] = "✅ Libro e inventario actualizados correctamente ($nuevos_totales totales / $nuevos_disponibles disponibles).";
                    $_SESSION['tipoMensaje'] = 'exito';
                    $stmt->close();
                } else {
                    $_SESSION['mensaje'] = 'Error al actualizar: ' . $conexion->error;
                    $_SESSION['tipoMensaje'] = 'error';
                }
            }
        } else {
            // MODO INSERTAR NUEVO LIBRO
            $sql_ins = "INSERT INTO libros 
                        (Titulo, Autor, Categoria, Editorial, ISBN, ano, `País`, Idioma, `Descripción`, `Encabezamiento de Materia`, `Clasificación`, Ejemplares_totales, Ejemplares_disponibles, Ejemplares_prestados) 
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 0)";
            $stmt = $conexion->prepare($sql_ins);
            $stmt->bind_param(
                "sssssisssssii",
                $titulo, $autor, $categoria, $editorial, $isbn,
                $ano, $pais, $idioma, $descripcion,
                $encabezamiento, $clasificacion,
                $nuevos_totales, $nuevos_totales
            );

            if ($stmt && $stmt->execute()) {
                $_SESSION['mensaje'] = "🎉 ¡Libro registrado exitosamente en la categoría '$categoria'!";
                $_SESSION['tipoMensaje'] = 'exito';
                $stmt->close();
            } else {
                $_SESSION['mensaje'] = 'Error al registrar en la base de datos: ' . $conexion->error;
                $_SESSION['tipoMensaje'] = 'error';
            }
        }
    }
    header("Location: ver_catalogo.php");
    exit();
}

// 3. PROCESAR BORRADO
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
                $_SESSION['mensaje'] = 'Error al eliminar del catálogo (verifica que no tenga préstamos activos).';
                $_SESSION['tipoMensaje'] = 'error';
            }
            $stmt_borrar->close();
        }
    }
    header("Location: ver_catalogo.php");
    exit();
}

// Campos dinámicos para evitar repeticiones
$campos_grid = [
    ['id' => 'input_Materia', 'name' => 'Encabezamiento_de_Materia', 'label' => 'Encabezamiento de Materia', 'type' => 'text', 'val' => $encabezamiento_de_materia, 'max' => 255],
    ['id' => 'input_Editorial', 'name' => 'Editorial', 'label' => 'Editorial', 'type' => 'text', 'val' => $editorial, 'max' => 100],
    ['id' => 'input_ISBN', 'name' => 'ISBN', 'label' => 'ISBN / Código', 'type' => 'text', 'val' => $isbn, 'max' => 50],
    ['id' => 'input_ano', 'name' => 'ano', 'label' => 'Año', 'type' => 'number', 'val' => $ano, 'max' => 9999],
    ['id' => 'input_clasificacion', 'name' => 'clasificacion', 'label' => 'Clasificación', 'type' => 'text', 'val' => $clasificacion, 'max' => 100],
    ['id' => 'input_Pais', 'name' => 'Pais', 'label' => 'País', 'type' => 'text', 'val' => $pais, 'max' => 50],
    ['id' => 'input_Idioma', 'name' => 'Idioma', 'label' => 'Idioma', 'type' => 'text', 'val' => $idioma, 'max' => 50],
];
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Libros - Biblioteca Sullivan</title>
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

            <div class="grid-dos-columnas" style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">

                <div class="grupo-campo">
                    <label>Título del Libro *</label>
                    <input type="text" id="input_Titulo" name="Titulo" value="<?php echo htmlspecialchars($titulo); ?>" maxlength="255" required>
                </div>

                <div class="grupo-campo">
                    <label>Autor *</label>
                    <input type="text" id="input_Autor" name="Autor" value="<?php echo htmlspecialchars($autor); ?>" maxlength="255" required>
                </div>

                <div class="grupo-campo">
                    <label>Categoría *</label>
                    <select name="Categoria" id="input_Categoria" required>
                        <?php foreach ($categorias_disponibles as $cat): ?>
                            <option value="<?php echo $cat; ?>" <?php echo ($categoria === $cat) ? 'selected' : ''; ?>>
                                <?php echo $cat; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <?php foreach ($campos_grid as $campo): ?>
                    <div class="grupo-campo">
                        <label><?php echo $campo['label']; ?></label>
                        <input type="<?php echo $campo['type']; ?>" 
                               id="<?php echo $campo['id']; ?>" 
                               name="<?php echo $campo['name']; ?>" 
                               value="<?php echo htmlspecialchars($campo['val']); ?>" 
                               <?php echo ($campo['type'] === 'number') ? 'max="' . $campo['max'] . '"' : 'maxlength="' . $campo['max'] . '"'; ?>>
                    </div>
                <?php endforeach; ?>

                <div class="grupo-campo" style="grid-column: span 2;">
                    <label>Descripción / Notas</label>
                    <input type="text" id="input_descripcion" name="descripcion" value="<?php echo htmlspecialchars($descripcion); ?>" maxlength="255">
                </div>

                <div class="grupo-campo" style="grid-column: span 2;">
                    <label>Ejemplares Totales *</label>
                    <input type="number" id="input_Ejemplares" name="Ejemplares_totales" value="<?php echo htmlspecialchars($ejemplares_totales); ?>" min="1" required>
                </div>

                <div class="grupo-campo" style="grid-column: span 2;">
                    <button type="submit" class="boton-guardar" style="width: 100%; padding: 14px; font-size: 15px; font-weight: bold; cursor: pointer;">
                        <?php echo $modo_editar ? 'Actualizar Libro e Inventario' : 'REGISTRAR LIBRO'; ?>
                    </button>
                </div>

                <?php if ($modo_editar): ?>
                    <div style="grid-column: span 2; text-align: center;">
                        <a href="ver_catalogo.php" style="color: #991B1B; font-weight: bold; text-decoration: none;">❌ Cancelar Edición</a>
                    </div>
                <?php endif; ?>

                <div style="grid-column: span 2; margin-top: 5px;">
                    <a href="ver_catalogo.php" style="display: block; text-align: center; font-weight: bold; color: white; text-decoration: none; background-color: #28a745; padding: 12px; border-radius: 5px;">
                        🔍 Ver Todos los Libros Registrados
                    </a>
                </div>

            </div>
        </form>
    </div>

    <div class="z1">
        <a class="z2" href="registro.php">REGRESAR</a>
    </div>
</body>
</html>