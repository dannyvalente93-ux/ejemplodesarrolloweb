<?php
session_start();
ini_set('display_errors', 1);
error_reporting(E_ALL);

require 'config.php';

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $correo = trim($_POST['correo']);
    $contraseña = isset($_POST['contraseña']) ? $_POST['contraseña'] : '';

    if (!empty($correo) && !empty($contraseña)) {
        try {
            $stmt = $conexion->prepare("SELECT * FROM usuarios WHERE correo = :correo");
            $stmt->bindParam(':correo', $correo);
            $stmt->execute();
            $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($usuario && password_verify($contraseña, $usuario['password'])) {
                $_SESSION['usuario_id'] = $usuario['id'];
                $_SESSION['usuario_nombre'] = $usuario['nombre'];
                header("Location: panel.php");
                exit;
            } else {
                $error = "Correo o contraseña incorrectos";
            }
        } catch (PDOException $e) {
            $error = "Error: " . $e->getMessage();
        }
    } else {
        $error = "Por favor completa todos los campos";
    }
}

if (!empty($error)) {
    echo "<p style='color:red; text-align:center; font-weight:bold;'>$error</p>";
    echo "<p style='text-align:center;'><a href='login.php'>Volver al inicio</a></p>";
}
?>