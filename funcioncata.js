function buscarLibroPorISBN() {
    const isbn = document.getElementById('isbn_input').value.trim();
    
    if (isbn === '') {
        alert('Por favor, escribe un número de ISBN primero.');
        return;
    }

    const tituloInput = document.getElementById('titulo_input');
    const autorInput = document.getElementById('autor_input');
    const editorialInput = document.getElementById('editorial_input');
    const anoInput = document.getElementById('ano_input');
    
    tituloInput.placeholder = "Buscando en Google Books...";

    // 🚀 URL corregida y exacta para conectar con Google Books
    fetch(`https://googleapis.com{isbn}`)
        .then(response => response.json())
        .then(data => {
            if (data.totalItems > 0) {
                // Tomamos el primer resultado que encuentre la API
                const infoLibro = data.items[0].volumeInfo;

                // Inyectamos los datos automáticamente en tus inputs
                tituloInput.value = infoLibro.title || '';
                
                if (infoLibro.authors) {
                    autorInput.value = infoLibro.authors.join(', ');
                } else {
                    autorInput.value = '';
                }

                editorialInput.value = infoLibro.publisher || infoLibro.editorial || '';
                
                if (infoLibro.publishedDate) {
                    anoInput.value = infoLibro.publishedDate.substring(0, 4);
                } else {
                    anoInput.value = '';
                }

                tituloInput.placeholder = "";
                alert('¡Libro encontrado y autocompletado con éxito! 🎉');
            } else {
                tituloInput.placeholder = "";
                alert('No se encontraron datos para ese ISBN en Google Books. Deberás registrarlo a mano.');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            tituloInput.placeholder = "";
            alert('Hubo un problema al conectar con el servicio de búsqueda.');
        });
}