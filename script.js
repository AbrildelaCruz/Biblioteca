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
// 1. Ruta absoluta desde la raíz de XAMPP para que funcione en cualquier pantalla del sistema
const rutaAudio = '/Sistema_biblioteca/Biblioteca/audio/click.mp3';
const sonidoClic = new Audio(rutaAudio);
sonidoClic.volume = 0.6; // Ajustamos el volumen a un nivel adecuado

// 2. Función segura para reproducir el efecto de sonido
function reproducirClic() {
    // Reiniciamos el tiempo a cero para permitir clics rápidos consecutivos
    sonidoClic.currentTime = 0;
    sonidoClic.play().catch(error => {
        console.log("El navegador requiere un clic previo en la pantalla para activar el audio.", error);
    });
}

// 3. Inyección automática del evento a todos los botones cuando cargue el DOM
document.addEventListener("DOMContentLoaded", () => {
    // Buscamos de golpe: botones, clases .btn, entradas de formulario (guardar/borrar) y enlaces del menú
    const elementosInteractivos = document.querySelectorAll('button, .btn, input[type="submit"], input[type="button"], a');
    
    elementosInteractivos.forEach(elemento => {
        // Le asignamos el sonido al hacer clic a cada uno de ellos
        elemento.addEventListener('click', reproducirClic);
    });

    // Truco informático: Desbloqueador automático para vencer el candado de seguridad del navegador
    const desbloquearAudio = () => {
        sonidoClic.play().then(() => {
            sonidoClic.pause(); // Lo reproduce en silencio un milisegundo para activarlo en el navegador
            window.removeEventListener('click', desbloquearAudio); // Quitamos el candado
        }).catch(() => {});
    };
    window.addEventListener('click', desbloquearAudio);

    console.log("Módulo UX de Audio Global inicializado con éxito en la Biblioteca Sullivan.");
});