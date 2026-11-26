<?php
session_start();
ini_set('display_errors', 1);
error_reporting(E_ALL);

if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit;
}

require 'config.php';

$error = "";
$exito = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = isset($_POST['nombre']) ? trim($_POST['nombre']) : '';
    $telefono = isset($_POST['telefono']) ? trim($_POST['telefono']) : '';
    $correo = isset($_POST['correo']) ? trim($_POST['correo']) : '';
    $ciudad = isset($_POST['ciudad']) ? trim($_POST['ciudad']) : '';
    $id_usuario = $_SESSION['usuario_id'];

    if (empty($nombre) || empty($correo)) {
        $error = "⚠️ Todos los campos obligatorios.";
    } else {
        try {
           $stmt = $conexion->prepare("INSERT INTO contactos (usuario_id, nombre, telefono, correo, ciudad) 
                            VALUES (:usuario_id, :nombre, :telefono, :correo, :ciudad)");
            
            $stmt->bindParam(':usuario_id', $_SESSION['usuario_id']);
            $stmt->bindParam(':nombre', $nombre);
            $stmt->bindParam(':telefono', $telefono);
            $stmt->bindParam(':correo', $correo);
            $stmt->bindParam(':ciudad', $ciudad);

            if ($stmt->execute()) {
                header("Location: panel.php");
                exit;
            }
        } catch (PDOException $e) {
            $error = "❌ Error: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Agregar Contacto</title>
    <style>
        body { font-family: Arial; max-width: 500px; margin: 50px auto; padding: 20px; }
        h2 { text-align: center; }
        input { width: 100%; padding: 10px; margin: 8px 0; box-sizing: border-box; border: 1px solid #ccc; border-radius: 4px; }
        button { background: #4CAF50; color: white; padding: 10px 20px; border: none; border-radius: 4px; font-size: 16px; cursor: pointer; }
        .error { color: red; background: #ffebee; padding: 10px; border-radius: 4px; margin-bottom: 15px; }
        a { color: #2196F3; text-decoration: none; }
    </style>
</head>
<body>
    <h2>➕ Agregar Nuevo Contacto</h2>

    <?php if ($error): ?>
        <div class="error"><?php echo $error; ?></div>
    <?php endif; ?>

    <form method="POST" action="contacto_registrar.php">
        Nombre completo:
        <input type="text" name="nombre" required>

        Teléfono:
        <input type="text" name="telefono">

        Correo electrónico:
        <input type="email" name="correo" required>

        Ciudad:
        <input type="text" name="ciudad">

        <button type="submit">Guardar Contacto</button>
    </form>

    <p><a href="panel.php">← Volver al panel</a></p>
</body>
</html>