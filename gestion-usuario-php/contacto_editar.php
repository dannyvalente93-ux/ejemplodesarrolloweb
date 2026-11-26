<?php require 'config.php';

// Obtener el contacto a editar
$id = $_GET['id'];
$stmt = $conexion->prepare("SELECT * FROM contactos WHERE id = :id");
$stmt->bindParam(':id', $id);
$stmt->execute();
$contacto = $stmt->fetch(PDO::FETCH_ASSOC);

// Obtener categorías
$stmt = $conexion->query("SELECT * FROM categorias ORDER BY nombre");
$categorias = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Guardar cambios
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = trim($_POST['nombre']);
    $correo = trim($_POST['correo']);
    $ciudad = trim($_POST['ciudad']);
    $categoria_id = $_POST['categoria_id'];
    $favorito = $_POST['favorito'];

    $stmt = $conexion->prepare("UPDATE contactos SET nombre=:nombre, correo=:correo, ciudad=:ciudad, categoria_id=:categoria_id, favorito=:favorito WHERE id=:id");
    $stmt->bindParam(':nombre', $nombre);
    $stmt->bindParam(':correo', $correo);
    $stmt->bindParam(':ciudad', $ciudad);
    $stmt->bindParam(':categoria_id', $categoria_id);
    $stmt->bindParam(':favorito', $favorito);
    $stmt->bindParam(':id', $id);
    $stmt->execute();
    
    header("Location: panel.php?mensaje=actualizado");
    exit;
}
?>

<form method="POST">
    <div>
        <label>Nombre:</label>
        <input type="text" name="nombre" value="<?php echo htmlspecialchars($contacto['nombre']); ?>" required>
    </div>
    <div>
        <label>Correo:</label>
        <input type="email" name="correo" value="<?php echo htmlspecialchars($contacto['correo']); ?>" required>
    </div>
    <div>
        <label>Ciudad:</label>
        <input type="text" name="ciudad" value="<?php echo htmlspecialchars($contacto['ciudad']); ?>" required>
    </div>
    <div>
        <label>Categoría:</label>
        <select name="categoria_id" required>
            <?php foreach ($categorias as $cat): ?>
                <option value="<?php echo $cat['id']; ?>" 
                    <?php echo $contacto['categoria_id'] == $cat['id'] ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($cat['nombre']); ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>
    <div>
        <label>¿Favorito?</label>
        <select name="favorito">
            <option value="No" <?php echo $contacto['favorito'] == 'No' ? 'selected' : ''; ?>>No</option>
            <option value="Si" <?php echo $contacto['favorito'] == 'Si' ? 'selected' : ''; ?>>Sí</option>
        </select>
    </div>
    <button type="submit">Guardar Cambios</button>
</form>
<a href="panel.php">← Volver</a>