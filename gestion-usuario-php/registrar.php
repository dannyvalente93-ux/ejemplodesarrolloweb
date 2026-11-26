<?php
session_start();
ini_set('display_errors', 1);
error_reporting(E_ALL);

require 'config.php';

$error = "";
$exito = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = isset($_POST['nombre']) ? trim($_POST['nombre']) : '';
    $correo = isset($_POST['correo']) ? trim($_POST['correo']) : '';
    $contraseña = isset($_POST['contraseña']) ? $_POST['contraseña'] : '';

    if (!empty($nombre) && !empty($correo) && !empty($contraseña)) {
        try {
            // Verificar si el correo ya existe
            $check = $conexion->prepare("SELECT id FROM usuarios WHERE correo = :correo");
            $check->bindParam(':correo', $correo);
            $check->execute();
            
            if ($check->rowCount() > 0) {
                $error = "Este correo ya está registrado";
            } else {
                // Encriptar contraseña
                $hash = password_hash($contraseña, PASSWORD_DEFAULT);
                
                // Insertar nuevo usuario
                $stmt = $conexion->prepare("INSERT INTO usuarios (nombre, correo, password) 
                                            VALUES (:nombre, :correo, :password)");
                $stmt->bindParam(':nombre', $nombre);
                $stmt->bindParam(':correo', $correo);
                $stmt->bindParam(':password', $hash);
                
                if ($stmt->execute()) {
                    $exito = "✅ Cuenta creada correctamente. Ya puedes <a href='login.php'>iniciar sesión</a>";
                }
            }
        } catch (PDOException $e) {
            $error = "Error: " . $e->getMessage();
        }
    } else {
        $error = "Por favor completa todos los campos";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registrarse</title>
    <style>
        body { font-family: Arial; max-width: 400px; margin: 50px auto; padding: 20px; }
        h2 { text-align: center; }
        input { width: 100%; padding: 8px; margin: 8px 0; box-sizing: border-box; }
        button { background: #4CAF50; color: white; padding: 10px; width: 100%; border: none; cursor: pointer; }
        .error { color: red; background: #ffebee; padding: 10px; border-radius: 4px; }
        .exito { color: green; background: #e8f5e9; padding: 10px; border-radius: 4px; }
    </style>
</head>
<body>
    <h2>📝 Crear Cuenta</h2>
    
    <?php if ($error): ?>
        <div class="error"><?php echo $error; ?></div>
    <?php endif; ?>
    
    <?php if ($exito): ?>
        <div class="exito"><?php echo $exito; ?></div>
    <?php endif; ?>

    <form method="POST" action="registrar.php">
        Nombre completo:
        <input type="text" name="nombre" required>
        
        Correo electrónico:
        <input type="email" name="correo" required>
        
        Contraseña:
        <input type="password" name="contraseña" required>
        
        <button type="submit">Registrarse</button>
    </form>
    
    <p style="text-align:center;">¿Ya tienes cuenta? <a href="login.php">Iniciar sesión</a></p>
</body>
</html>