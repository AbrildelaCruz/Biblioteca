<?php
require_once 'conexion.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$mensaje = $_SESSION['mensaje'] ?? '';
$tipoMensaje = $_SESSION['tipoMensaje'] ?? '';
unset($_SESSION['mensaje'], $_SESSION['tipoMensaje']);

// 1. Cargar alumnos, profesores y libros para los desplegables
$alumnos_res = $conexion->query("SELECT id_Alumnos, Nombre FROM alumnos ORDER BY Nombre ASC");
$profesores_res = $conexion->query("SELECT id_profesores, Nombre FROM profesores ORDER BY Nombre ASC");
// Solo traer libros que tengan ejemplares disponibles
$libros_res = $conexion->query("SELECT id_libro, Titulo FROM libros WHERE Ejemplares_disponibles > 0 ORDER BY Titulo ASC");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrar Salida de Libro - Biblioteca Sullivan</title>
    <link rel="stylesheet" href="style.css?v=<?php echo time(); ?>">
</head>
<body class="body2">

    <div class="contenedor-burbujas">
        <div class="burbuja"></div>
        <div class="burbuja"></div>
        <div class="burbuja"></div>
    </div>

    <?php if ($mensaje !== ''): ?>
        <div class="alerta <?php echo $tipoMensaje; ?>" style="padding: 10px; margin: 15px auto; max-width: 700px; text-align: center; border-radius: 5px; font-weight: bold; background-color: <?php echo $tipoMensaje === 'exito' ? '#d4edda' : '#f8d7da'; ?>; color: <?php echo $tipoMensaje === 'exito' ? '#155724' : '#721c24'; ?>;">
            <?php echo $mensaje; ?>
        </div>
    <?php endif; ?>

    <div class="contenedor-formulario" style="max-width: 600px;">
        <h2>Formulario de Préstamos / Apartados</h2>
        
        <!-- Envía los datos directos al archivo procesar_movimiento.php que ya creamos -->
        <form action="procesar_movimiento.php" method="POST" style="display: flex; flex-direction: column; gap: 15px;">
            
            <div class="grupo-campo">
                <label>1. Tipo de Usuario</label>
                <select name="tipo_usuario" required onchange="alternarFormulario(this)">
                    <option value="">-- Selecciona una opción --</option>
                    <option value="alumno">Alumno</option>
                    <option value="profesor">Profesor</option>
                </select>
            </div>

            <!-- Selector de Alumnos (Oculto por defecto) -->
            <div class="grupo-campo" id="bloque_alumno" style="display: none;">
                <label>2. Selecciona al Alumno</label>
                <select name="id_usuario" id="select_alumno">
                    <option value="">-- Buscar Alumno --</option>
                    <?php while($al = $alumnos_res->fetch_assoc()): ?>
                        <option value="<?php echo $al['id_Alumnos']; ?>"><?php echo htmlspecialchars($al['Nombre']); ?></option>
                    <?php endwhile; ?>
                </select>
            </div>

            <!-- Selector de Profesores (Oculto por defecto) -->
            <div class="grupo-campo" id="bloque_profesor" style="display: none;">
                <label>2. Selecciona al Docente</label>
                <select name="id_usuario" id="select_profesor" disabled>
                    <option value="">-- Buscar Profesor --</option>
                    <?php while($prof = $profesores_res->fetch_assoc()): ?>
                        <option value="<?php echo $prof['id_profesores']; ?>"><?php echo htmlspecialchars($prof['Nombre']); ?></option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="grupo-campo">
                <label>3. Libro a llevarse</label>
                <select name="id_libro" required>
                    <option value="">-- Buscar Título del Libro --</option>
                    <?php if ($libros_res && $libros_res->num_rows > 0): ?>
                        <?php while($lib = $libros_res->fetch_assoc()): ?>
                            <option value="<?php echo $lib['id_libro']; ?>"><?php echo htmlspecialchars($lib['Titulo']); ?></option>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <option value="" disabled>❌ No hay libros disponibles en estanterías</option>
                    <?php endif; ?>
                </select>
            </div>

            <div class="grupo-campo">
                <label>4. Acción a realizar</label>
                <select name="accion" required>
                    <option value="prestar">Prestar Libro (Entrega en 9 días)</option>
                    <option value="apartar">Apartar Libro (Reservar por 48 horas)</option>
                </select>
            </div>

            <button type="submit" class="boton-guardar" style="width: 100%; margin-top: 10px;">
                🚀 Registrar Movimiento
            </button>
        </form>
    </div>

    <div class="z1">
        <a class="z2" href="registros.html">REGRESAR</a>
    </div>

    <!-- SCRIPT DE INTERACCIÓN LIMPINA -->
    <script>
    function alternarFormulario(control) {
        var bloqueAl = document.getElementById('bloque_alumno');
        var bloqueProf = document.getElementById('bloque_profesor');
        var selectAl = document.getElementById('select_alumno');
        var selectProf = document.getElementById('select_profesor');

        if (control.value === 'alumno') {
            bloqueAl.style.display = 'block';
            selectAl.disabled = false;
            selectAl.required = true;

            bloqueProf.style.display = 'none';
            selectProf.disabled = true;
            selectProf.required = false;
        } else if (control.value === 'profesor') {
            bloqueProf.style.display = 'block';
            selectProf.disabled = false;
            selectProf.required = true;

            bloqueAl.style.display = 'none';
            selectAl.disabled = true;
            selectAl.required = false;
        } else {
            bloqueAl.style.display = 'none';
            bloqueProf.style.display = 'none';
            selectAl.disabled = true;
            selectProf.disabled = true;
        }
    }
    </script>
    <div class="z1">
        <a class="z2" href="registro.html">REGRESAR</a>
  </div>
</body>
</html>