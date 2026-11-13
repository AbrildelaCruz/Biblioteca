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

    <div class="contenedor-formulario" style="max-width: 680px;">
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

            <!-- 2. Buscador por Matrícula o Nombre -->
            <div class="grupo-campo" id="bloque_busqueda" style="display: none;">
                <label id="etiqueta_busqueda">2. Buscar por Matrícula *</label>
                <div style="display: flex; gap: 8px;">
                    <input type="text" id="input_matricula" autocomplete="off" placeholder="Escribe la matrícula (ej. DOC-26-001) o nombre..." onkeyup="buscarPorMatricula()">
                    <button type="button" id="btn_cambiar_usuario" onclick="limpiarSeleccionUsuario()" style="display: none; background: #DC3545; color: white; border: none; border-radius: 8px; padding: 0 14px; font-weight: bold; cursor: pointer;">Cambiar</button>
                </div>
                
                <!-- Lista de coincidencias -->
                <div id="lista_coincidencias" style="display: none; border: 1px solid #CBD5E1; border-radius: 8px; background: #FFFFFF; max-height: 180px; overflow-y: auto; box-shadow: 0 4px 12px rgba(0,0,0,0.08); margin-top: 5px;"></div>

                <!-- Campos ocultos que viajan al servidor -->
                <input type="hidden" name="id_alumno" id="hidden_id_alumno" disabled>
                <input type="hidden" name="id_profesor" id="hidden_id_profesor" disabled>
            </div>

            <!-- CAMPOS AUTOCOMPLETADOS DE ALUMNO -->
            <div id="datos_auto_alumno" class="grid-dos-columnas" style="display: none; grid-template-columns: 1fr 1fr; gap: 12px; background: #F8FAFC; padding: 15px; border-radius: 8px; border: 1px solid #DDE6ED;">
                <div class="grupo-campo">
                    <label>Matrícula</label>
                    <input type="text" id="auto_al_matricula" readonly>
                </div>
                <div class="grupo-campo">
                    <label>Nombre Completo</label>
                    <input type="text" id="auto_al_nombre" readonly>
                </div>
                <div class="grupo-campo">
                    <label>Madre Encargada</label>
                    <input type="text" id="auto_al_madre" readonly>
                </div>
                <div class="grupo-campo">
                    <label>Familia</label>
                    <input type="text" id="auto_al_familia" readonly>
                </div>
                <div class="grupo-campo">
                    <label>Piso</label>
                    <input type="text" id="auto_al_piso" readonly>
                </div>
                <div class="grupo-campo">
                    <label>Grupo</label>
                    <input type="text" id="auto_al_grupo" readonly>
                </div>
            </div>

            <!-- CAMPOS AUTOCOMPLETADOS DE PROFESOR -->
            <div id="datos_auto_profesor" class="grid-dos-columnas" style="display: none; grid-template-columns: 1fr 1fr; gap: 12px; background: #F8FAFC; padding: 15px; border-radius: 8px; border: 1px solid #DDE6ED;">
                <div class="grupo-campo">
                    <label>Matrícula Docente</label>
                    <input type="text" id="auto_prof_matricula" readonly>
                </div>
                <div class="grupo-campo">
                    <label>Nombre del Profesor</label>
                    <input type="text" id="auto_prof_nombre" readonly>
                </div>
                <div class="grupo-campo" style="grid-column: 1 / -1;">
                    <label>Academia / Materia(s)</label>
                    <input type="text" id="auto_prof_materia" readonly>
                </div>
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
            input.placeholder = 'Escribe la matrícula o el nombre del alumno...';
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
                        const sub = (tipo === 'alumno') ? `Grupo: ${item.grupo} | Familia: ${item.familia}` : item.materia;
                        opcion.innerHTML = `<strong style="color:#0F4C81;">[${item.matricula}]</strong> <b>${item.nombre}</b> <br><small style="color:#64748B;">${sub}</small>`;
                        
                        opcion.onmouseover = () => opcion.style.backgroundColor = '#F1F5F9';
                        opcion.onmouseout = () => opcion.style.backgroundColor = '#FFFFFF';
                         opcion.onclick = () => seleccionarUsuario(tipo, item);
                        
                        lista.appendChild(opcion);
                    });
                } else {
                    lista.innerHTML = `<div style="padding: 10px; color: #991B1B; font-size: 13px; text-align: center;">❌ No se encontró ningún ${tipo} con esa matrícula.</div>`;
                }
                lista.style.display = 'block';
            })
            .catch(err => console.error('Error al buscar usuario:', err));
    }

    function seleccionarUsuario(tipo, item) {
        const input = document.getElementById('input_matricula');
        const btnCambiar = document.getElementById('btn_cambiar_usuario');
        const lista = document.getElementById('lista_coincidencias');
        const hiddenAl = document.getElementById('hidden_id_alumno');
        const hiddenProf = document.getElementById('hidden_id_profesor');

        input.value = `[${item.matricula}] ${item.nombre}`;
        input.readOnly = true;
        btnCambiar.style.display = 'inline-block';
        lista.style.display = 'none';

        if (tipo === 'alumno') {
            hiddenAl.value = item.id;
            hiddenAl.disabled = false;
            hiddenProf.value = '';
            hiddenProf.disabled = true;

            document.getElementById('auto_al_matricula').value = item.matricula;
            document.getElementById('auto_al_nombre').value = item.nombre;
            document.getElementById('auto_al_madre').value = item.madre;
            document.getElementById('auto_al_familia').value = item.familia;
            document.getElementById('auto_al_piso').value = item.piso;
            document.getElementById('auto_al_grupo').value = item.grupo;

            document.getElementById('datos_auto_alumno').style.display = 'grid';
            document.getElementById('datos_auto_profesor').style.display = 'none';
        } else {
            hiddenProf.value = item.id;
            hiddenProf.disabled = false;
            hiddenAl.value = '';
            hiddenAl.disabled = true;

            document.getElementById('auto_prof_matricula').value = item.matricula;
            document.getElementById('auto_prof_nombre').value = item.nombre;
            document.getElementById('auto_prof_materia').value = item.materia;

            document.getElementById('datos_auto_profesor').style.display = 'grid';
            document.getElementById('datos_auto_alumno').style.display = 'none';
        }
    }

    function limpiarSeleccionUsuario() {
        const input = document.getElementById('input_matricula');
        const btnCambiar = document.getElementById('btn_cambiar_usuario');
        const lista = document.getElementById('lista_coincidencias');
        const hiddenAl = document.getElementById('hidden_id_alumno');
        const hiddenProf = document.getElementById('hidden_id_profesor');

        input.value = '';
        input.readOnly = false;
        btnCambiar.style.display = 'none';
        lista.style.display = 'none';
        lista.innerHTML = '';
        hiddenAl.value = '';
        hiddenAl.disabled = true;
        hiddenProf.value = '';
        hiddenProf.disabled = true;

        document.getElementById('datos_auto_alumno').style.display = 'none';
        document.getElementById('datos_auto_profesor').style.display = 'none';
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