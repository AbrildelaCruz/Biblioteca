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
        <div class="alerta <?php echo $tipoMensaje; ?>"
            style="padding: 10px; margin: 15px auto; max-width: 700px; text-align: center; border-radius: 5px; font-weight: bold; background-color: <?php echo $tipoMensaje === 'exito' ? '#d4edda' : '#f8d7da'; ?>; color: <?php echo $tipoMensaje === 'exito' ? '#155724' : '#721c24'; ?>;">
            <?php echo $mensaje; ?>
        </div>
    <?php endif; ?>

    <div class="contenedor-formulario" style="max-width: 700px;">
        <h2>Formulario de Préstamos Inteligente</h2>

        <form action="procesar_movimiento.php" method="POST">
            <!-- Llaves Primarias Ocultas para inyectar directo a la Base de Datos -->
            <input type="hidden" id="id_usuario_final" name="id_usuario" value="">
            <input type="hidden" id="id_libro_final" name="id_libro" value="">

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; text-align: left;">
                
                <!-- 1. SELECCIÓN DE ROL -->
                <div class="grupo-campo" style="grid-column: span 2;">
                    <label>1. Tipo de Usuario (Rol)</label>
                    <select name="tipo_usuario" id="rol_usuario" required onchange="alternarCamposRol(this.value)">
                        <option value="">-- Selecciona una opción --</option>
                        <option value="alumno">Alumno</option>
                        <option value="profesor">Profesor</option>
                    </select>
                </div>

                <!-- BLOQUE BÚSQUEDA AUTOMÁTICA DE USUARIO -->
                <div class="grupo-campo" id="bloque_buscador_usuario" style="grid-column: span 2; display: none;">
                    <label id="label_buscar_usuario">Nombre a buscar</label>
                    <div class="campo-buscar-inline">
                        <input type="text" id="input_nombre_buscar" placeholder="Teclea el nombre y presiona buscar...">
                        <button type="button" class="boton-buscar-ajax" onclick="buscarUsuarioPorAJAX()">🔍 Buscar</button>
                    </div>
                </div>

                <!-- CAMPOS AUTOCOMPLETADOS DEL ALUMNO -->
                <div class="grupo-campo bloque-alumno" style="display: none;">
                    <label>Madre Encargada</label>
                    <input type="text" id="input_Madre" readonly style="background-color: #f8fafc; color: #64748b;">
                </div>
                <div class="grupo-campo bloque-alumno" style="display: none;">
                    <label>Familia</label>
                    <input type="text" id="input_Familia" readonly style="background-color: #f8fafc; color: #64748b;">
                </div>
                <div class="grupo-campo bloque-alumno" style="display: none;">
                    <label>Piso</label>
                    <input type="text" id="input_Piso" readonly style="background-color: #f8fafc; color: #64748b;">
                </div>
                <div class="grupo-campo bloque-alumno" style="display: none;">
                    <label>Grupo</label>
                    <input type="text" id="input_Grupo" readonly style="background-color: #f8fafc; color: #64748b;">
                </div>

                <!-- CAMPOS AUTOCOMPLETADOS DEL PROFESOR -->
                <div class="grupo-campo bloque-profesor" style="display: none; grid-column: span 2;">
                    <label>Academia / Materia(s) Asignada(s)</label>
                    <input type="text" id="input_Materia" readonly style="background-color: #f8fafc; color: #64748b;">
                </div>

                <!-- BLOQUE BÚSQUEDA AUTOMÁTICA DEL LIBRO -->
                <div class="grupo-campo" style="grid-column: span 2; border-top: 1px dashed #ccc; padding-top: 15px; margin-top: 5px;">
                    <label>3. Título del Libro a llevarse</label>
                    <div class="campo-buscar-inline">
                        <input type="text" id="input_Titulo" placeholder="Escribe el nombre del libro para localizarlo...">
                        <button type="button" class="boton-buscar-ajax" style="background-color: #4a148c;" onclick="buscarLibroPorAJAX()">🔍 Localizar</button>
                    </div>
                </div>

                <div class="grupo-campo">
                    <label>Autor</label>
                    <input type="text" id="input_Autor" readonly style="background-color: #f8fafc; color: #64748b;">
                </div>
                <div class="grupo-campo">
                    <label>Editorial</label>
                    <input type="text" id="input_Editorial" readonly style="background-color: #f8fafc; color: #64748b;">
                </div>
                <div class="grupo-campo">
                    <label>ISBN</label>
                    <input type="text" id="input_ISBN" readonly style="background-color: #f8fafc; color: #64748b;">
                </div>
                <div class="grupo-campo">
                    <label>Idioma</label>
                    <input type="text" id="input_Idioma" readonly style="background-color: #f8fafc; color: #64748b;">
                </div>

                <div class="grupo-campo" style="grid-column: span 2;">
                    <label>4. Acción a realizar</label>
                    <select name="accion" required>
                        <option value="prestar">Prestar Libro (Entrega en 9 días)</option>
                        <option value="apartar">Apartar Libro (Reservar por 48 horas)</option>
                    </select>
                </div>
            </div>

            <button type="submit" class="boton-guardar" style="width: 100%; margin-top: 15px; background-color: #2A9D8F;">
                🚀 Registrar Movimiento Completo
            </button>
        </form>
    </div>

    <div class="z1">
        <a class="z2" href="registro.php">REGRESAR</a>
    </div>

    <!-- 🚀 LÓGICA DE INTERACCIÓN INTERACTIVA ASÍNCRONA -->
    <script>
        function alternarCamposRol(rol) {
            document.getElementById('id_usuario_final').value = "";
            document.getElementById('input_nombre_buscar').value = "";
            document.getElementById('input_Madre').value = "";
            document.getElementById('input_Familia').value = "";
            document.getElementById('input_Piso').value = "";
            document.getElementById('input_Grupo').value = "";
            document.getElementById('input_Materia').value = "";

            var buscadorUsuario = document.getElementById('bloque_buscador_usuario');
            var alumnosCampos = document.querySelectorAll('.bloque-alumno');
            var profesoresCampos = document.querySelectorAll('.bloque-profesor');

            if (rol === 'alumno' || rol === 'profesor') {
                buscadorUsuario.style.display = 'block';
                document.getElementById('label_buscar_usuario').innerText = "2. Buscar Nombre del " + rol.toUpperCase();
                
                alumnosCampos.forEach(el => el.style.display = (rol === 'alumno') ? 'block' : 'none');
                profesoresCampos.forEach(el => el.style.display = (rol === 'profesor') ? 'block' : 'none');
            } else {
                buscadorUsuario.style.display = 'none';
                alumnosCampos.forEach(el => el.style.display = 'none');
                profesoresCampos.forEach(el => el.style.display = 'none');
            }
        }

        function buscarUsuarioPorAJAX() {
            const rol = document.getElementById('rol_usuario').value;
            const nombre = document.getElementById('input_nombre_buscar').value.trim();

            if (nombre === "") {
                alert("Por favor, escribe un nombre primero.");
                return;
            }

            fetch('buscar_usuario.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ rol: rol, nombre: nombre })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success && data.data) {
                    // Mapea las mayúsculas exactas de tus tablas relacionales
                    document.getElementById('id_usuario_final').value = data.data.id_Alumnos || data.data.id_profesores;
                    document.getElementById('input_nombre_buscar').value = data.data.Nombre;
                    
                    if (rol === 'alumno') {
                        document.getElementById('input_Madre').value = data.data.Madre_encargada || '-';
                        document.getElementById('input_Familia').value = data.data.Familia || '-';
                        document.getElementById('input_Piso').value = data.data.Piso || '-';
                        document.getElementById('input_Grupo').value = data.data.Grupo || '-';
                    } else {
                        document.getElementById('input_Materia').value = data.data.Materia || '-';
                    }
                } else {
                    alert(data.message || 'No se encontró el registro.');
                }
            })
            .catch(err => console.error('Error:', err));
        }
        function buscarLibroPorAJAX() {
            const titulo = document.getElementById('input_Titulo').value.trim();

            if (titulo === "") {
                alert("Por favor, escribe el título del libro.");
                return;
            }

            // Mapeo adaptado a lo que lee tu script 'buscar_libro.php'
            const payload = { Titulo: titulo, Autor: '', Editorial: '', ISBN: '' };

            fetch('buscar_libro.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(payload)
            })
            .then(res => res.json())
            .then(data => {
                if (data.success && data.libro) {
                    // Inyectamos las llaves primarias y datos reales en las cajas bloqueadas
                    document.getElementById('id_libro_final').value = data.libro.id_libro;
                    document.getElementById('input_Titulo').value = data.libro.Titulo;
                    document.getElementById('input_Autor').value = data.libro.Autor || '-';
                    document.getElementById('input_Editorial').value = data.libro.Editorial || '-';
                    document.getElementById('input_ISBN').value = data.libro.ISBN || '-';
                    document.getElementById('input_Idioma').value = data.libro.Idioma || '-';
                    alert('📚 Libro localizado en las estanterías e inyectado con éxito.');
                } else {
                    alert('No se encontró ningún libro con ese título.');
                }
            })
            .catch(err => {
                console.error('Error:', err);
                alert("Error al procesar la búsqueda del libro.");
            });
        }
    </script>
    <script>
document.addEventListener("DOMContentLoaded", () => {
    // 1. Verificamos si existen datos guardados por el botón "Renovar"
    const rol = sessionStorage.getItem('renovar_rol');
    const nombre = sessionStorage.getItem('renovar_nombre');
    const libro = sessionStorage.getItem('renovar_libro');

    if (rol && nombre && libro) {
        // 2. Rellenamos el selector de rol e invocamos tu función nativa para abrir los campos
        const selectRol = document.getElementById('rol_usuario');
        if (selectRol) {
            selectRol.value = rol;
            alternarCamposRol(rol); // Ejecuta tu lógica para mostrar bloques de Alumno/Profesor
        }

        // 3. Inyectamos el nombre del usuario y disparamos tu buscador AJAX nativo
        const inputBuscarUser = document.getElementById('input_nombre_buscar');
        if (inputBuscarUser) {
            inputBuscarUser.value = nombre;
            buscarUsuarioPorAJAX(); // Busca en tu base de datos y rellena los campos bloqueados
        }

        // 4. Inyectamos el título del libro y disparamos tu localizador AJAX nativo
        const inputBuscarLibro = document.getElementById('input_Titulo');
        if (inputBuscarLibro) {
            inputBuscarLibro.value = libro;
            buscarLibroPorAJAX(); // Busca el libro en las estanterías e inyecta las llaves ocultas
        }

        // 5. Limpiamos la memoria temporal para que el formulario vuelva a la normalidad en la próxima carga
        sessionStorage.removeItem('renovar_rol');
        sessionStorage.removeItem('renovar_nombre');
        sessionStorage.removeItem('renovar_libro');
    }
});
</script>
</body>
</html>