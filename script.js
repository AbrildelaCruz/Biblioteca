document.getElementById('btn-entrar').addEventListener('click', function() {
    const contenedor = document.getElementById('pantalla-biblioteca');
    const interfaz = document.querySelector('.interfaz-bienvenida');

    // 1. Desvanecer el botón inmediatamente
    interfaz.classList.add('ocultar');

    // 2. Cambiar a puertas Semiabiertas tras 300 milisegundos
    setTimeout(function() {
        contenedor.classList.remove('fondo-cerrado');
        contenedor.classList.add('fondo-semiabierto');
    }, 300);

    // 3. Cambiar a puertas Abiertas tras 800 milisegundos totales
    setTimeout(function() {
        contenedor.classList.remove('fondo-semiabierto');
        contenedor.classList.add('fondo-abierto');
    }, 900);

    // 4. Redireccionar al bloque de módulos tras 1.7 segundos (cuando ya se aprecie el interior)
    setTimeout(function() {
        window.location.href = 'registro.php'; // Cambia por el nombre de tu archivo de módulos
    }, 1800);
});