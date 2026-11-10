document.getElementById('btn-entrar').addEventListener('click', function() {
    const contenedor = document.getElementById('pantalla-biblioteca');
    const interfaz = document.querySelector('.interfaz-bienvenida');

    // 1. Desvanecer el botón inmediatamente
    interfaz.classList.add('ocultar');

    // 2. Cambiar a puertas Semiabiertas tras 300 milisegundos
    setTimeout(function() {
        contenedor.classList.remove('puertas-cerradas');
        contenedor.classList.add('puertas-semiabiertas');
    }, 300);

    // 3. Cambiar a puertas Abiertas tras 800 milisegundos totales
    setTimeout(function() {
        contenedor.classList.remove('puertas-semiabiertas');
        contenedor.classList.add('puertas-abiertas');
    }, 900);

    // 4. Redireccionar al bloque de módulos tras 1.7 segundos (cuando ya se aprecie el interior)
    setTimeout(function() {
        window.location.href = 'registro.php'; // Cambia por el nombre de tu archivo de módulos
    }, 1800);
});
// 1. Ruta corregida respetando mayúsculas y minúsculas exactas de tu proyecto
const rutaAudioGlobal = '/Sistema_biblioteca/Biblioteca/audio/click.mp3';
const audioBoton = new Audio(rutaAudioGlobal);
audioBoton.volume = 0.5; // Volumen adecuado

// 2. Función para reproducir el sonido de clic
function reproducirClic() {
    audioBoton.currentTime = 0; // Reinicia el audio para clics rápidos
    audioBoton.play().catch(error => {
        console.log("El navegador bloqueó el audio temporalmente.", error);
    });
}

// 3. Asignación automática del evento a todos los botones del sistema
document.addEventListener("DOMContentLoaded", () => {
    // Buscamos todos los botones, clases de diseño, inputs de envío y enlaces del menú
    const elementosInteractivos = document.querySelectorAll('button, .btn, input[type="submit"], input[type="button"], a');
    
    elementosInteractivos.forEach(elemento => {
        // Inyectamos el evento click a cada uno de ellos de forma automatizada
        elemento.addEventListener('click', reproducirClic);
    });

    // Desbloqueador automático para el candado de seguridad de Chrome/Edge
    const activarAudioNavegador = () => {
        audioBoton.play().then(() => {
            audioBoton.pause(); // Reproduce un milisegundo en silencio para dar permiso
            window.removeEventListener('click', activarAudioNavegador);
        }).catch(() => {});
    };
    window.addEventListener('click', activarAudioNavegador);

    console.log("Módulo UX de Audio Global inicializado correctamente.");
});