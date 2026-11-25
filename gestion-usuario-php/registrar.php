<?php require 'config.php'; ?>

<!-- ... tu código existente ... -->

<form method="POST" action="contacto_registrar.php">
    <!-- Tus campos existentes: nombre, correo, ciudad, etc. -->
    
    <div>
        <label>Categoría:</label>
        <select name="categoria_id" required>
            <option value="">Selecciona una categoría</option>
            <?php
            $stmt = $conexion->query("SELECT * FROM categorias ORDER BY nombre");
            $categorias = $stmt->fetchAll(PDO::FETCH_ASSOC);
            foreach ($categorias as $cat) {
                echo "<option value='{$cat['id']}'>{$cat['nombre']}</option>";
            }
            ?>
        </select>
    </div>

    <div>
        <label>¿Favorito?</label>
        <select name="favorito">
            <option value="No">No</option>
            <option value="Si">Sí</option>
        </select>
    </div>

    <button type="submit">Guardar Contacto</button>
</form>

<?php
// Procesar el formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = trim($_POST['nombre']);
    $correo = trim($_POST['correo']);
    $ciudad = trim($_POST['ciudad']);
    $categoria_id = $_POST['categoria_id'];
    $favorito = $_POST['favorito'];

    $stmt = $conexion->prepare("INSERT INTO contactos (nombre, correo, ciudad, categoria_id, favorito) VALUES (:nombre, :correo, :ciudad, :categoria_id, :favorito)");
    $stmt->bindParam(':nombre', $nombre);
    $stmt->bindParam(':correo', $correo);
    $stmt->bindParam(':ciudad', $ciudad);
    $stmt->bindParam(':categoria_id', $categoria_id);
    $stmt->bindParam(':favorito', $favorito);
    
    if ($stmt->execute()) {
        header("Location: panel.php?mensaje=registrado");
        exit;
    }
}
?>