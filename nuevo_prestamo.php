<?php
require_once 'conexion.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$mensaje = $_SESSION['mensaje'] ?? '';
$tipoMensaje = $_SESSION['tipoMensaje'] ?? '';
unset($_SESSION['mensaje'], $_SESSION['tipoMensaje']);

$libros_res = $conexion->query("SELECT id_libro, Titulo, Autor, Ejemplares_disponibles FROM libros WHERE Ejemplares_disponibles > 0 ORDER BY Titulo ASC");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrar Préstamo por Matrícula - Biblioteca Sullivan</title>
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
        <div class="alerta <?php echo $tipoMensaje; ?>" style="padding: 12px; margin: 15px auto; max-width: 700px; text-align: center; border-radius: 6px; font-weight: bold; background-color: <?php echo $tipoMensaje === 'exito' ? '#d4edda' : '#f8d7da'; ?>; color: <?php echo $tipoMensaje === 'exito' ? '#155724' : '#721c24'; ?>;">
            <?php echo $mensaje; ?>
        </div>
    <?php endif; ?>

    <div class="contenedor-formulario" style="max-width: 620px;">
        <h2>📋 Registrar Préstamo / Apartado</h2>
        
        <form action="procesar_movimiento.php" method="POST" id="formPrestamo" onsubmit="return validarSeleccionUsuario()" style="display: flex; flex-direction: column; gap: 15px;">
            
            <!-- 1. Tipo de Usuario -->
            <div class="grupo-campo">
                <label>1. Tipo de Usuario *</label>
                <select name="tipo_usuario" id="tipo_usuario" required onchange="reiniciarBuscadorUsuario()">
                    <option value="">-- Selecciona Alumno o Profesor --</option>
                    <option value="alumno">Alumno</option>
                    <option value="profesor">Profesor</option>
                </select>
            </div>

            <!-- 2. Buscador por Matrícula (o Nombre) -->
            <div class="grupo-campo" id="bloque_busqueda" style="display: none;">
                <label id="etiqueta_busqueda">2. Buscar por Matrícula *</label>
                <input type="text" id="input_matricula" autocomplete="off" placeholder="Escribe la matrícula (ej. DOC-26-001) o nombre..." onkeyup="buscarPorMatricula()">
                
                <!-- Lista de coincidencias en tiempo real -->
                <div id="lista_coincidencias" style="display: none; border: 1px solid #CBD5E1; border-radius: 8px; background: #FFFFFF; max-height: 180px; overflow-y: auto; box-shadow: 0 4px 12px rgba(0,0,0,0.08);"></div>

                <!-- Tarjeta de confirmación del usuario seleccionado -->
                <div id="usuario_seleccionado" style="display: none; margin-top: 8px; padding: 10px 14px; background: #E8F5E9; border: 1px solid #A5D6A7; border-radius: 8px; color: #1B5E20; font-size: 14px;">
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <span id="texto_seleccionado"></span>
                        <button type="button" onclick="limpiarSeleccionUsuario()" style="background: #DC3545; color: white; border: none; border-radius: 4px; padding: 4px 8px; font-size: 11px; cursor: pointer; font-weight: bold;">Cambiar</button>
                    </div>
                </div>

                <!-- Campos ocultos que viajan al servidor -->
                <input type="hidden" name="id_alumno" id="hidden_id_alumno" disabled>
                <input type="hidden" name="id_profesor" id="hidden_id_profesor" disabled>
            </div>

            <!-- 3. Selección del Libro -->
            <div class="grupo-campo">
                <label>3. Libro a llevarse *</label>
                <select name="id_libro" required>
                    <option value="">-- Selecciona el Título del Libro --</option>
                    <?php if ($libros_res && $libros_res->num_rows > 0): ?>
                        <?php while($lib = $libros_res->fetch_assoc()): ?>
                            <option value="<?php echo $lib['id_libro']; ?>">
                                <?php echo htmlspecialchars($lib['Titulo']); ?> — <?php echo htmlspecialchars($lib['Autor']); ?> (<?php echo $lib['Ejemplares_disponibles']; ?> disp.)
                            </option>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <option value="" disabled>❌ No hay libros disponibles en estanterías</option>
                    <?php endif; ?>
                </select>
            </div>

            <!-- 4. Tipo de Movimiento -->
            <div class="grupo-campo">
                <label>4. Acción a realizar *</label>
                <select name="accion" required>
                    <option value="prestar">Prestar Libro (Entrega en 7 días)</option>
                    <option value="apartar">Apartar Libro (Reservar por 48 horas)</option>
                </select>
            </div>

            <button type="submit" class="boton-guardar" style="width: 100%; margin-top: 10px;">
                🚀 Registrar Movimiento
            </button>
        </form>
    </div>

    <div class="z1">
        <a class="z2" href="registro.php">REGRESAR</a>
    </div>

    <script>
    function reiniciarBuscadorUsuario() {
        const tipo = document.getElementById('tipo_usuario').value;
        const bloque = document.getElementById('bloque_busqueda');
        const etiqueta = document.getElementById('etiqueta_busqueda');
        const input = document.getElementById('input_matricula');

        limpiarSeleccionUsuario();

        if (tipo === 'alumno') {
            bloque.style.display = 'block';
            etiqueta.textContent = '2. Buscar Alumno por Matrícula (o Nombre) *';
            input.placeholder = 'Escribe la matrícula escolar o el nombre del alumno...';
            input.focus();
        } else if (tipo === 'profesor') {
            bloque.style.display = 'block';
            etiqueta.textContent = '2. Buscar Profesor por Matrícula (ej. DOC-26-001) *';
            input.placeholder = 'Escribe DOC-26-001 o el nombre del docente...';
            input.focus();
        } else {
            bloque.style.display = 'none';
        }
    }

    function buscarPorMatricula() {
        const tipo = document.getElementById('tipo_usuario').value;
        const q = document.getElementById('input_matricula').value.trim();
        const lista = document.getElementById('lista_coincidencias');

        if (tipo === '' || q === '') {
            lista.style.display = 'none';
            lista.innerHTML = '';
            return;
        }

        fetch('buscar_usuario.php?tipo=' + encodeURIComponent(tipo) + '&q=' + encodeURIComponent(q))
            .then(res => res.json())
            .then(datos => {
                lista.innerHTML = '';
                if (datos.length > 0) {
                    datos.forEach(item => {
                        const opcion = document.createElement('div');
                        opcion.style.padding = '10px 12px';
                        opcion.style.borderBottom = '1px solid #E2E8F0';
                        opcion.style.cursor = 'pointer';
                        opcion.style.fontSize = '13px';
                        opcion.innerHTML = `<strong style="color:#0F4C81;">[${item.matricula}]</strong> <b>${item.nombre}</b> <br><small style="color:#64748B;">${item.detalle}</small>`;
                        
                        opcion.onmouseover = () => opcion.style.backgroundColor = '#F1F5F9';
                        opcion.onmouseout = () => opcion.style.backgroundColor = '#FFFFFF';
                        opcion.onclick = () => seleccionarUsuario(tipo, item.id, item.matricula, item.nombre);
                        
                        lista.appendChild(opcion);
                    });
                } else {
                    lista.innerHTML = `<div style="padding: 10px; color: #991B1B; font-size: 13px; text-align: center;">❌ No se encontró ningún ${tipo} con esa matrícula.</div>`;
                }
                lista.style.display = 'block';
            })
            .catch(err => console.error('Error al buscar usuario:', err));
    }

    function seleccionarUsuario(tipo, id, matricula, nombre) {
        const input = document.getElementById('input_matricula');
        const lista = document.getElementById('lista_coincidencias');
        const tarjeta = document.getElementById('usuario_seleccionado');
        const texto = document.getElementById('texto_seleccionado');
        const hiddenAl = document.getElementById('hidden_id_alumno');
        const hiddenProf = document.getElementById('hidden_id_profesor');

        input.style.display = 'none';
        lista.style.display = 'none';
        tarjeta.style.display = 'block';
        texto.innerHTML = `✅ Seleccionado: <b>[${matricula}] ${nombre}</b>`;

        if (tipo === 'alumno') {
            hiddenAl.value = id;
            hiddenAl.disabled = false;
            hiddenProf.value = '';
            hiddenProf.disabled = true;
        } else {
            hiddenProf.value = id;
            hiddenProf.disabled = false;
            hiddenAl.value = '';
            hiddenAl.disabled = true;
        }
    }

    function limpiarSeleccionUsuario() {
        const input = document.getElementById('input_matricula');
        const lista = document.getElementById('lista_coincidencias');
        const tarjeta = document.getElementById('usuario_seleccionado');
        const hiddenAl = document.getElementById('hidden_id_alumno');
        const hiddenProf = document.getElementById('hidden_id_profesor');

        input.value = '';
        input.style.display = 'block';
        lista.style.display = 'none';
        lista.innerHTML = '';
        tarjeta.style.display = 'none';
        hiddenAl.value = '';
        hiddenAl.disabled = true;
        hiddenProf.value = '';
        hiddenProf.disabled = true;
    }

    function validarSeleccionUsuario() {
        const hiddenAl = document.getElementById('hidden_id_alumno');
        const hiddenProf = document.getElementById('hidden_id_profesor');

        if (hiddenAl.disabled && hiddenProf.disabled) {
            alert('⚠️ Por favor busca y selecciona un usuario de la lista antes de registrar el movimiento.');
            return false;
        }
        return true;
    }
    </script>
</body>
</html>