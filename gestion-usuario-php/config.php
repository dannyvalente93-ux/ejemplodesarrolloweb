<?php
$servidor = "sql313.infinityfree.com";
$usuario = "if0_43534964";
$contraseña = "i6TdFL5xgdCM"; // Pon tu contraseña real entre las comillas
$base_datos = "if0_43534964_ejemplosdesarrolloweb";

try {
    $conexion = new PDO("mysql:host=$servidor;dbname=$base_datos;charset=utf8", $usuario, $contraseña);
    $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Error de conexión: " . $e->getMessage());
}
?>