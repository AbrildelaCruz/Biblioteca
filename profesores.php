<?php
require_once 'conexion.php';

// Iniciar sesión si no está iniciada para los mensajes
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$mensaje = $_SESSION['mensaje'] ?? '';
$tipoMensaje = $_SESSION['tipoMensaje'] ?? '';
unset($_SESSION['mensaje'], $_SESSION['tipoMensaje']);

// 1. PROCESAR EL FORMULARIO POR MÉTODO POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $Nombre = trim($_POST['Nombre'] ?? '');
    
    // Recibimos las materias como un arreglo y las unimos con comas
    $materias_seleccionadas = $_POST['Materia'] ?? [];
    $Materia = is_array($materias_seleccionadas) ? implode(', ', $materias_seleccionadas) : trim($materias_seleccionadas);

    // Validar que los campos obligatorios no estén vacíos
    if ($Nombre === '' || $Materia === '') {
        $_SESSION['mensaje'] = 'Completa todos los campos obligatorios (Nombre y Materias).';
        $_SESSION['tipoMensaje'] = 'error';
    } else {
        try {
            // Verificar si el profesor ya existe con ese mismo nombre y exactamente ese mismo grupo de materias
            $stmt_prof = $conexion->prepare("SELECT id_Profesores FROM profesores WHERE Nombre = ? AND Materia = ?");
            $stmt_prof->bind_param("ss", $Nombre, $Materia);
            $stmt_prof->execute();
            $res_prof = $stmt_prof->get_result();
            
            if ($res_prof->num_rows > 0) {
                $_SESSION['mensaje'] = 'Este docente ya se encuentra registrado con las mismas materias.';
                $_SESSION['tipoMensaje'] = 'error';
            } else {
                // Registrar el nuevo profesor de forma directa
                $stmt_ins_prof = $conexion->prepare("INSERT INTO profesores (Nombre, Materia) VALUES (?, ?)");
                $stmt_ins_prof->bind_param("ss", $Nombre, $Materia);
                $stmt_ins_prof->execute();
                $stmt_ins_prof->close();
                
                $_SESSION['mensaje'] = 'Docente registrado correctamente.';
                $_SESSION['tipoMensaje'] = 'exito';
            }
            $stmt_prof->close();

        } catch (Exception $e) {
            $_SESSION['mensaje'] = 'Error al registrar el docente: ' . $e->getMessage();
            $_SESSION['tipoMensaje'] = 'error';
        }
    }
    header("Location: profesores.php");
    exit();
}

// 2. ACCIÓN DE ELIMINAR DOCENTE (Si es necesario)
if (isset($_GET['eliminar_id'])) {
    $id_a_borrar = filter_input(INPUT_GET, 'eliminar_id', FILTER_VALIDATE_INT);
    if ($id_a_borrar) {
        $stmt_borrar = $conexion->prepare('DELETE FROM profesores WHERE id_Profesores = ?');
        if ($stmt_borrar) {
            $stmt_borrar->bind_param('i', $id_a_borrar);
            if ($stmt_borrar->execute()) {
                $_SESSION['mensaje'] = 'Docente eliminado correctamente.';
                $_SESSION['tipoMensaje'] = 'exito';
            } else {
                $_SESSION['mensaje'] = 'Error al eliminar el docente de la base de datos.';
                $_SESSION['tipoMensaje'] = 'error';
            }
            $stmt_borrar->close();
        }
    }
    header("Location: profesores.php");
    exit();
}

// 3. CONSULTA SENCILLA PARA LA TABLA VISUAL
$resultado = $conexion->query('SELECT id_Profesores, Nombre, Materia FROM profesores ORDER BY id_Profesores DESC');
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de profesores</title>
    <link rel="stylesheet" href="style.css?v=<?php echo time(); ?>">
    <link rel="icon" type="image/png" href="logobiblioteca.png">
    
    <!-- Enlace CSS de Choices.js completo -->
    <link rel="stylesheet" href="https://jsdelivr.net"/>

    <style>
        .choices__list--multiple .choices__item {
            background-color: #4a148c !important; 
            border: 1px solid #4a148c !important;
            border-radius: 4px !important;
        }
        .choices__inner {
            border-radius: 5px !important;
            border: 1px solid #ccc !important;
            background-color: #fff !important;
            text-align: left;
        }
    </style>
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
    <div class="alerta <?php echo $tipoMensaje; ?>" style="padding: 10px; margin: 15px auto; max-width: 600px; text-align: center; border-radius: 5px; font-weight: bold; background-color: <?php echo $tipoMensaje === 'exito' ? '#d4edda' : '#f8d7da'; ?>; color: <?php echo $tipoMensaje === 'exito' ? '#155724' : '#721c24'; ?>;">
        <?php echo $mensaje; ?>
    </div>
    <?php endif; ?>

    <div class="contenedor-formulario">
        <h2>Registrar Docente</h2>
        
        <form action="profesores.php" method="POST">
            <div class="grupo-campo">
                <label>Nombre del Profesor</label>
                <input type="text" name="Nombre" maxlength="100" required>
            </div>
            
            <div class="grupo-campo">
                <label>Materia(s)</label>
                <select name="Materia[]" id="materias" multiple required>
                    <option value="Conciencia Historica">Conciencia Historica</option>
                    <option value="Filosofia/Humanidades">Filosofia/Humanidades</option>
                    <option value="Natacion">Natacion</option>
                    <option value="Matematicas">Matematicas</option>
                    <option value="Lengua y Comunicacion">Lengua y Comunicacion</option>
                </select>
            </div>
            
            <button type="submit" class="boton-guardar">Guardar Profesor</button>
        </form> 
    </div> 

    <!-- Tabla de visualización simplificada -->
    <div style="max-width: 800px; margin: 30px auto; padding: 0 10px;">
        <table border="1" style="width:100%; text-align:center; border-collapse: collapse; background: white; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
            <thead>
                <tr style="background-color: #4a148c; color: white;">
                    <th style="padding: 10px;">ID</th>
                    <th style="padding: 10px;">Nombre del Profesor</th>
                    <th style="padding: 10px;">Materia(s)</th>
                    <th style="padding: 10px;">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($resultado && $resultado->num_rows > 0): ?>
                    <?php while ($row = $resultado->fetch_assoc()): ?>
                        <tr>
                            <td style="padding: 10px;"><?php echo $row['id_Profesores']; ?></td>
                            <td style="padding: 10px;"><?php echo htmlspecialchars($row['Nombre']); ?></td>
                            <td style="padding: 10px;"><?php echo htmlspecialchars($row['Materia']); ?></td>
                            <td style="padding: 10px;">
                                <a href="editar_profesor.php?id=<?php echo $row['id_Profesores']; ?>" class="boton-editar" style="margin-right: 5px;">Editar</a>
                                <a href="profesores.php?eliminar_id=<?php echo $row['id_Profesores']; ?>" class="boton-eliminar" onclick="return confirm('¿Seguro que deseas eliminar a este docente?');">Eliminar</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4" style="padding: 15px;">No hay docentes registrados.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <div class="z1" style="text-align: center; margin-top: 20px; margin-bottom: 40px;">
        <a class="z2" href="registro.html" style="text-decoration: none; font-weight: bold;">REGRESAR</a>
    </div>

    <!-- Enlace JS de Choices.js completo -->
    <script src="https://jsdelivr.net"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const elementoSelect = document.getElementById('materias');
            new Choices(elementoSelect, {
                removeItemButton: true, 
                noChoicesText: 'No hay más materias disponibles',
                itemSelectText: 'Presiona para seleccionar',
                placeholder: true,
                placeholderValue: '-- Selecciona las materias --'
            });
        });
    </script>
</body>
</html>