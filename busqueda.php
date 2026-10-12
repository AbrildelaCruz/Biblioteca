<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
</head>
<body >
    <h1>Biblioteca Sullivan</h1>
     <form action="/buscar-libro" method="get">
     <input  type="search" name="termino" placeholder="titulo, auctor o tema" required >
     <select  name="libro">
        <option value="todos">todos los campos</option>
         <option value="titulo">titulo</option>
         <option value="tema">tema</option>
          <option value="titulo">auctor</option> 
          <option value="titulo">isbm</option>
           <button type="submit">bucar</button>
    
    </form>
</body>
</html>