const boton = document.getElementById('btn-modificar');
const texto = document.getElementById('grupo-campo');

boton.addEventListener('click',() => {
    if(boton.textContent.includes('Modificar')){
        const contenidoActual = Text.textContent;
        texto.inneHTML= '<iput type ="text" id="inputEditando" value="${contenidoActual}" style="font-size:16px; padding:5px">';
        boton.innerHTML = 'Guardar';
        boton.style.backgroundColor = #
    }
});