<?php
require_once 'conexion.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$mensaje = $_SESSION['mensaje'] ?? '';
$tipoMensaje = $_SESSION['tipoMensaje'] ?? '';
unset($_SESSION['mensaje'], $_SESSION['tipoMensaje']);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrar Préstamo - Biblioteca Sullivan</title>
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

    <div class="contenedor-formulario" style="max-width: 700px;">
        <h2>📋 Registrar Préstamo / Apartado</h2>
        
        <form action="procesar_movimiento.php" method="POST" id="formPrestamo" onsubmit="return validarFormularioCompleto()" style="display: flex; flex-direction: column; gap: 15px;">
            
            <!-- 1. Tipo de Usuario -->
            <div class="grupo-campo">
                <label>1. Tipo de Usuario *</label>
                <select name="tipo_usuario" id="tipo_usuario" required onchange="reiniciarBuscadorUsuario()">
                    <option value="">-- Selecciona Alumno o Profesor --</option>
                    <option value="alumno">Alumno</option>
                    <option value="profesor">Profesor</option>
                </select>
            </div>

            <!-- 2. Buscador de Usuario por Matrícula o Nombre -->
            <div class="grupo-campo" id="bloque_busqueda" style="display: none;">
                <label id="etiqueta_busqueda">2. Buscar por Matrícula *</label>
                <div style="display: flex; gap: 8px;">
                    <input type="text" id="input_matricula" autocomplete="off" placeholder="Escribe matrícula o nombre..." onkeyup="buscarPorMatricula()">
                    <button type="button" id="btn_cambiar_usuario" onclick="limpiarSeleccionUsuario()" style="display: none; background: #DC3545; color: white; border: none; border-radius: 8px; padding: 0 14px; font-weight: bold; cursor: pointer;">Cambiar</button>
                </div>
                <div id="lista_coincidencias" style="display: none; border: 1px solid #CBD5E1; border-radius: 8px; background: #FFFFFF; max-height: 180px; overflow-y: auto; box-shadow: 0 4px 12px rgba(0,0,0,0.08); margin-top: 5px;"></div>

                <input type="hidden" name="id_alumno" id="hidden_id_alumno" disabled>
                <input type="hidden" name="id_profesor" id="hidden_id_profesor" disabled>
            </div>

            <!-- Datos Autocompletados de Alumno -->
            <div id="datos_auto_alumno" class="grid-dos-columnas" style="display: none; grid-template-columns: 1fr 1fr; gap: 12px; background: #F8FAFC; padding: 15px; border-radius: 8px; border: 1px solid #DDE6ED;">
                <div class="grupo-campo"><label>Matrícula</label><input type="text" id="auto_al_matricula" readonly></div>
                <div class="grupo-campo"><label>Nombre Completo</label><input type="text" id="auto_al_nombre" readonly></div>
                <div class="grupo-campo"><label>Madre Encargada</label><input type="text" id="auto_al_madre" readonly></div>
                <div class="grupo-campo"><label>Familia</label><input type="text" id="auto_al_familia" readonly></div>
                <div class="grupo-campo"><label>Piso</label><input type="text" id="auto_al_piso" readonly></div>
                <div class="grupo-campo"><label>Grupo</label><input type="text" id="auto_al_grupo" readonly></div>
            </div>

            <!-- Datos Autocompletados de Profesor -->
            <div id="datos_auto_profesor" class="grid-dos-columnas" style="display: none; grid-template-columns: 1fr 1fr; gap: 12px; background: #F8FAFC; padding: 15px; border-radius: 8px; border: 1px solid #DDE6ED;">
                <div class="grupo-campo"><label>Matrícula Docente</label><input type="text" id="auto_prof_matricula" readonly></div>
                <div class="grupo-campo"><label>Nombre del Profesor</label><input type="text" id="auto_prof_nombre" readonly></div>
                <div class="grupo-campo" style="grid-column: 1 / -1;"><label>Academia / Materia(s)</label><input type="text" id="auto_prof_materia" readonly></div>
            </div>

            <!-- 3. Buscador de Libro por Título, Autor o ISBN -->
            <div class="grupo-campo">
                <label>3. Buscar Libro (por Título, Autor o ISBN) *</label>
                <div style="display: flex; gap: 8px;">
                    <input type="text" id="input_buscar_libro" autocomplete="off" placeholder="Escribe el título, autor o ISBN del libro..." onkeyup="buscarLibroAuto()">
                    <button type="button" id="btn_cambiar_libro" onclick="limpiarSeleccionLibro()" style="display: none; background: #DC3545; color: white; border: none; border-radius: 8px; padding: 0 14px; font-weight: bold; cursor: pointer;">Cambiar</button>
                </div>
                <div id="lista_coincidencias_libro" style="display: none; border: 1px solid #CBD5E1; border-radius: 8px; background: #FFFFFF; max-height: 180px; overflow-y: auto; box-shadow: 0 4px 12px rgba(0,0,0,0.08); margin-top: 5px;"></div>

                <input type="hidden" name="id_libro" id="hidden_id_libro" disabled>
            </div>

            <!-- Datos Autocompletados del Libro -->
            <div id="datos_auto_libro" class="grid-dos-columnas" style="display: none; grid-template-columns: 1fr 1fr; gap: 12px; background: #F8FAFC; padding: 15px; border-radius: 8px; border: 1px solid #DDE6ED;">
                <div class="grupo-campo" style="grid-column: 1 / -1;"><label>Título del Libro</label><input type="text" id="auto_lib_titulo" readonly></div>
                <div class="grupo-campo"><label>Autor</label><input type="text" id="auto_lib_autor" readonly></div>
                <div class="grupo-campo"><label>Categoría</label><input type="text" id="auto_lib_categoria" readonly></div>
                <div class="grupo-campo"><label>Editorial</label><input type="text" id="auto_lib_editorial" readonly></div>
                <div class="grupo-campo"><label>ISBN / Disponibles</label><input type="text" id="auto_lib_isbn_disp" readonly></div>
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
    // --- LÓGICA DE USUARIO ---
    function reiniciarBuscadorUsuario() {
        const tipo = document.getElementById('tipo_usuario').value;
        const bloque = document.getElementById('bloque_busqueda');
        const etiqueta = document.getElementById('etiqueta_busqueda');
        const input = document.getElementById('input_matricula');

        limpiarSeleccionUsuario();

        if (tipo === 'alumno') {
            bloque.style.display = 'block';
            etiqueta.textContent = '2. Buscar Alumno por Matrícula o Nombre *';
            input.placeholder = 'Escribe matrícula o nombre del alumno...';
            input.focus();
        } else if (tipo === 'profesor') {
            bloque.style.display = 'block';
            etiqueta.textContent = '2. Buscar Profesor por Matrícula (ej. DOC-26-001) *';
            input.placeholder = 'Escribe DOC-26-001 o nombre del docente...';
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
                        opcion.style.cssText = 'padding: 10px 12px; border-bottom: 1px solid #E2E8F0; cursor: pointer; font-size: 13px;';
                        const sub = (tipo === 'alumno') ? `Grupo: ${item.grupo} | Familia: ${item.familia}` : item.materia;
                        opcion.innerHTML = `<strong style="color:#0F4C81;">[${item.matricula}]</strong> <b>${item.nombre}</b> <br><small style="color:#64748B;">${sub}</small>`;
                        opcion.onmouseover = () => opcion.style.backgroundColor = '#F1F5F9';
                        opcion.onmouseout = () => opcion.style.backgroundColor = '#FFFFFF';
                        opcion.onclick = () => seleccionarUsuario(tipo, item);
                        lista.appendChild(opcion);
                    });
                } else {
                    lista.innerHTML = `<div style="padding: 10px; color: #991B1B; font-size: 13px; text-align: center;">❌ No se encontró ningún ${tipo}.</div>`;
                }
                lista.style.display = 'block';
            });
    }

    function seleccionarUsuario(tipo, item) {
        const input = document.getElementById('input_matricula');
        input.value = `[${item.matricula}] ${item.nombre}`;
        input.readOnly = true;
        document.getElementById('btn_cambiar_usuario').style.display = 'inline-block';
        document.getElementById('lista_coincidencias').style.display = 'none';

        const hiddenAl = document.getElementById('hidden_id_alumno');
        const hiddenProf = document.getElementById('hidden_id_profesor');

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
        input.value = '';
        input.readOnly = false;
        document.getElementById('btn_cambiar_usuario').style.display = 'none';
        document.getElementById('lista_coincidencias').style.display = 'none';
        document.getElementById('hidden_id_alumno').disabled = true;
        document.getElementById('hidden_id_profesor').disabled = true;
        document.getElementById('datos_auto_alumno').style.display = 'none';
        document.getElementById('datos_auto_profesor').style.display = 'none';
    }

    // --- LÓGICA DE LIBRO ---
    function buscarLibroAuto() {
        const q = document.getElementById('input_buscar_libro').value.trim();
        const lista = document.getElementById('lista_coincidencias_libro');

        if (q === '') {
            lista.style.display = 'none';
            lista.innerHTML = '';
            return;
        }

        fetch('buscar_libro.php?q=' + encodeURIComponent(q))
            .then(res => res.json())
            .then(libros => {
                lista.innerHTML = '';
                if (libros.length > 0) {
                    libros.forEach(lib => {
                        const opcion = document.createElement('div');
                        opcion.style.cssText = 'padding: 10px 12px; border-bottom: 1px solid #E2E8F0; cursor: pointer; font-size: 13px;';
                        opcion.innerHTML = `<strong style="color:#0F4C81;">${lib.titulo}</strong> — ${lib.autor} <br><small style="color:#166534; font-weight:bold;">[${lib.categoria}] • ${lib.disponibles} disponibles</small>`;
                        opcion.onmouseover = () => opcion.style.backgroundColor = '#F1F5F9';
                        opcion.onmouseout = () => opcion.style.backgroundColor = '#FFFFFF';
                        opcion.onclick = () => seleccionarLibro(lib);
                        lista.appendChild(opcion);
                    });
                } else {
                    lista.innerHTML = `<div style="padding: 10px; color: #991B1B; font-size: 13px; text-align: center;">❌ No se encontraron libros disponibles con esa búsqueda.</div>`;
                }
                lista.style.display = 'block';
            });
    }

    function seleccionarLibro(lib) {
        const input = document.getElementById('input_buscar_libro');
        input.value = lib.titulo;
        input.readOnly = true;
        document.getElementById('btn_cambiar_libro').style.display = 'inline-block';
        document.getElementById('lista_coincidencias_libro').style.display = 'none';

        const hiddenLib = document.getElementById('hidden_id_libro');
        hiddenLib.value = lib.id;
        hiddenLib.disabled = false;

        document.getElementById('auto_lib_titulo').value = lib.titulo;
        document.getElementById('auto_lib_autor').value = lib.autor;
        document.getElementById('auto_lib_categoria').value = lib.categoria;
        document.getElementById('auto_lib_editorial').value = lib.editorial;
        document.getElementById('auto_lib_isbn_disp').value = `ISBN: ${lib.isbn} (${lib.disponibles} disp.)`;

        document.getElementById('datos_auto_libro').style.display = 'grid';
    }

    function limpiarSeleccionLibro() {
        const input = document.getElementById('input_buscar_libro');
        input.value = '';
        input.readOnly = false;
        document.getElementById('btn_cambiar_libro').style.display = 'none';
        document.getElementById('lista_coincidencias_libro').style.display = 'none';
        document.getElementById('hidden_id_libro').disabled = true;
        document.getElementById('datos_auto_libro').style.display = 'none';
    }

    // --- VALIDACIÓN FINAL ANTES DE ENVIAR ---
    function validarFormularioCompleto() {
        const hiddenAl = document.getElementById('hidden_id_alumno');
        const hiddenProf = document.getElementById('hidden_id_profesor');
        const hiddenLib = document.getElementById('hidden_id_libro');

        if (hiddenAl.disabled && hiddenProf.disabled) {
            alert('⚠️ Por favor busca y selecciona un Alumno o Profesor de la lista.');
            return false;
        }
        if (hiddenLib.disabled) {
            alert('⚠️ Por favor busca y selecciona un Libro disponible de la lista.');
            return false;
        }
        return true;
    }
    </script>
</body>
</html>