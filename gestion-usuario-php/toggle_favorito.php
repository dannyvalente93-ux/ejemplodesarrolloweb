<?php
require 'config.php';

$id = $_GET['id'];
$volver = isset($_GET['volver']) ? $_GET['volver'] : 'panel.php';

// Obtener estado actual y cambiarlo
$stmt = $conexion->prepare("SELECT favorito FROM contactos WHERE id = :id");
$stmt->bindParam(':id', $id);
$stmt->execute();
$contacto = $stmt->fetch(PDO::FETCH_ASSOC);

$nuevo_estado = $contacto['favorito'] === 'Si' ? 'No' : 'Si';

// Actualizar
$stmt = $conexion->prepare("UPDATE contactos SET favorito = :favorito WHERE id = :id");
$stmt->bindParam(':favorito', $nuevo_estado);
$stmt->bindParam(':id', $id);
$stmt->execute();

header("Location: $volver");
exit;
?>