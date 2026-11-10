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
// 1. Instanciar el efecto de sonido (debes guardar un archivo .mp3 corto en tu carpeta)
const sonidoClic = new Audio('audio/click.mp3');

// Configurar el volumen (un valor entre 0 y 1, 0.4 es ideal para que no asuste)
sonidoClic.volume = 0.4;

// 2. Función global para reproducir el sonido
function reproducirClic() {
    // Reiniciar el audio a cero por si el usuario da clics muy rápido
    sonidoClic.currentTime = 0; 
    sonidoclic.play();
}

// 3. Asignar el sonido automáticamente a TODOS los botones del sistema
document.addEventListener("DOMContentLoaded", () => {
    // Buscamos todos los botones y elementos con la clase .btn-animado
    const botones = document.querySelectorAll('button, .btn, input[type="submit"]');
    
    botones.forEach(boton => {
        boton.addEventListener('click', reproducirClic);
    });
});