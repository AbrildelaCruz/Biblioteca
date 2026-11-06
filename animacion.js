// Esperamos a que el DOM esté completamente cargado
document.addEventListener('DOMContentLoaded', () => {
    const btnEntrar = document.getElementById('btnEntrar');
    const contenedorPuertas = document.getElementById('contenedorPuertas');

    // Escuchamos el clic en el botón de entrada
    btnEntrar.addEventListener('click', () => {
        // 1. Añade la clase CSS que activa la animación de apertura de puertas
        contenedorPuertas.classList.add('abierto');

        // 2. Espera 1.5 segundos (1500ms) a que termine la animación antes de redirigir
        setTimeout(() => {
            window.location.href = "tu-siguiente-pagina.html"; // <-- Reemplaza con tu URL de destino
        }, 1500);
    });
});