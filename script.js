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
// El sistema detecta solito si estás en localhost
const servidorBase = window.location.origin;
// Creamos la ruta dinámica exacta hacia tu audio
const rutaPerfecta = servidorBase + '/Sistema_biblioteca/Biblioteca/audio/click.mp3';

console.log("Intentando conectar con el audio en: ", rutaPerfecta);

const sonidoBoton = new Audio(rutaPerfecta);
sonidoBoton.volume = 0.7;

// Forzar la carga del archivo desde que abre la página
sonidoBoton.load();

function reproducirClic() {
    sonidoBoton.currentTime = 0;
    sonidoBoton.play()
        .then(() => console.log("¡Sonido reproducido con éxito! 🎵"))
        .catch(error => {
            console.error("Fallo al reproducir audio. El error es: ", error.message);
        });
}

document.addEventListener("DOMContentLoaded", () => {
    // Escucha clics en CUALQUIER botón o elemento interactivo del sistema
    const selectores = 'button, .btn, input[type="submit"], input[type="button"], a, .btn-animado';
    const elementos = document.querySelectorAll(selectores);
    
    elementos.forEach(elemento => {
        elemento.addEventListener('click', reproducirClic);
    });

    // Desbloqueador por si el navegador está de terco
    const desbloquear = () => {
        sonidoBoton.play().then(() => {
            sonidoBoton.pause();
            window.removeEventListener('click', desbloquear);
        }).catch(() => {});
    };
    window.addEventListener('click', desbloquear);
});