<?php
session_start();
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Si el usuario ya inició sesión, lo enviamos al panel
if (isset($_SESSION['usuario_id'])) {
    header("Location: panel.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 400px; margin: 80px auto; padding: 20px; }
        h2 { text-align: center; }
        input { width: 100%; padding: 10px; margin: 8px 0; box-sizing: border-box; border: 1px solid #ccc; border-radius: 4px; }
        button { background: #2196F3; color: white; padding: 10px; width: 100%; border: none; border-radius: 4px; font-size: 16px; cursor: pointer; }
        button:hover { background: #1976D2; }
        .error { color: white; background: #f44336; padding: 10px; border-radius: 4px; text-align: center; margin-bottom: 15px; }
        a { color: #2196F3; text-decoration: none; }
        p { text-align: center; margin-top: 20px; }
    </style>
</head>
<body>
    <h2>🔐 Iniciar Sesión</h2>

    <?php if (isset($_GET['error'])): ?>
        <div class="error">Correo o contraseña incorrectos</div>
    <?php endif; ?>

    <form method="POST" action="autenticar.php">
        Correo electrónico:
        <input type="email" name="correo" required>

        Contraseña:
        <input type="password" name="contraseña" required>

        <button type="submit">Ingresar</button>
    </form>

    <p>¿No tienes cuenta? <a href="registrar.php">Regístrate aquí</a></p>
</body>
</html>