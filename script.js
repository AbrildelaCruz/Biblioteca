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
// RASTREADOR DE AUDIO - BIBLIOTECA SULLIVAN
const urlAudio = window.location.origin + '/Sistema_biblioteca/Biblioteca/audio/click.mp3';

console.log("%c[INFORMATICA] Intentando conectar con el audio en: " + urlAudio, "color: cyan; font-weight: bold; font-size: 14px;");

const audioPrueba = new Audio(urlAudio);
audioPrueba.volume = 0.7;

// Evento que avisa si el archivo de sonido SÍ se encontró en XAMPP
audioPrueba.addEventListener('canplaythrough', () => {
    console.log("%c[EXITO] ¡El archivo click.mp3 se cargó correctamente en el servidor! 🎉", "color: lime; font-weight: bold; font-size: 14px;");
}, { once: true });

// Evento que avisa si el archivo NO se encontró o la ruta está mal
audioPrueba.addEventListener('error', (e) => {
    console.error("%c[ERROR 404] No se encontró el archivo de audio. Revisa que esté en htdocs/Sistema_biblioteca/Biblioteca/audio/click.mp3 y que no tenga doble .mp3", "color: red; font-weight: bold; font-size: 14px;");
});

function ejecutarSonido() {
    audioPrueba.currentTime = 0;
    audioPrueba.play()
        .then(() => console.log("Sonido reproducido en el botón."))
        .catch(err => console.log("Navegador bloqueó el audio temporalmente hasta dar un clic general.", err.message));
}

document.addEventListener("DOMContentLoaded", () => {
    // Aplica el sonido a absolutamente todos los botones y enlaces
    const elementos = document.querySelectorAll('button, .btn, input[type="submit"], input[type="button"], a');
    elementos.forEach(el => {
        el.addEventListener('click', ejecutarSonido);
    });
    
    // Desbloqueador automático para Chrome/Edge
    const desbloquear = () => {
        audioPrueba.play().then(() => {
            audioPrueba.pause();
            window.removeEventListener('click', desbloquear);
        }).catch(() => {});
    };
    window.addEventListener('click', desbloquear);
});