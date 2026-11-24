<?php
session_start();

// Si ya inició sesión, mandarlo directo a los módulos
if (isset($_SESSION['admin_autenticado']) && $_SESSION['admin_autenticado'] === true) {
    header("Location: registro.php");
    exit();
}

// 🔑 CONFIGURA AQUÍ TU CONTRASEÑA DE ACCESO
$CONTRASENA_SISTEMA = "biblioteca"; // Cambia esto a la contraseña que desees
$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $clave_ingresada = $_POST['password'] ?? '';

    if ($clave_ingresada === $CONTRASENA_SISTEMA) {
        $_SESSION['admin_autenticado'] = true;
        header("Location: registro.php");
        exit();
    } else {
        $error = "❌ Contraseña incorrecta. Intenta de nuevo.";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acceso - Biblioteca Sullivan</title>
    <link rel="stylesheet" href="style.css">
    <link rel="icon" type="image/png" href="logobiblioteca.png">
</head>
<body class="body2" style="display: flex; justify-content: center; align-items: center; min-height: 100vh; background-color: #0d1b2a; padding-bottom: 0 !important;">

    <div class="contenedor-formulario" style="max-width: 400px; margin: 20px; text-align: center; border-top: 5px solid #b38f43;">
        <img src="logobiblioteca.png" alt="Logo Biblioteca" style="width: 70px; margin-bottom: 10px;" onerror="this.style.display='none'">
        <h2 style="margin-bottom: 8px; font-size: 20px;">🔒 Acceso Restringido</h2>
        <p style="color: #64748B; font-size: 14px; margin-bottom: 20px;">Ingresa la contraseña del bibliotecario para acceder a los módulos.</p>

        <?php if ($error !== ""): ?>
            <div style="background: #FEE2E2; color: #991B1B; padding: 10px; border-radius: 6px; margin-bottom: 15px; font-size: 13px; font-weight: bold;">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="login.php" style="display: flex; flex-direction: column; gap: 15px;">
            <div class="grupo-campo" style="text-align: left;">
                <label for="password">Contraseña del Sistema</label>
                <input type="password" id="password" name="password" placeholder="Escribe la contraseña..." required autofocus>
            </div>

            <button type="submit" class="boton-guardar" style="width: 100%; margin-top: 5px; background-color: #0F4C81;">
                Entrar al Panel
            </button>
        </form>

        <div style="margin-top: 18px;">
            <a href="index.html" style="color: #64748B; font-size: 13px; text-decoration: none;">← Volver a la portada</a>
        </div>
    </div>

</body>
</html>