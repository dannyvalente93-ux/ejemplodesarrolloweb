<?php
session_start();
ini_set('display_errors', 1);
error_reporting(E_ALL);

// ✅ Inicializar variables
$busca = "";
$direccion = "ASC";  
// Verificar sesión
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit;
}

require 'config.php';

// ✅ Si el buscador funciona con URL, úsalo así:
$busca = isset($_GET['busca']) ? trim($_GET['busca']) : '';
$direccion = isset($_GET['direccion']) ? $_GET['direccion'] : 'ASC';

// ... resto de tu código


// Variables DENTRO del código PHP
$busca = isset($_GET['busca']) ? trim($_GET['busca']) : '';
$orden = isset($_GET['orden']) ? $_GET['orden'] : 'nombre';

// Verificar si el usuario inició sesión
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit;
}

require 'config.php';

// ... resto de tu código

// Obtener los contactos del usuario
try {
    $stmt = $conexion->prepare("SELECT * FROM contactos WHERE usuario_id = :usuario_id ORDER BY id DESC");
    $stmt->bindParam(':usuario_id', $_SESSION['usuario_id']);
    $stmt->execute();
    $contactos = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = "Error: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Contactos</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 900px; margin: 0 auto; padding: 20px; }
        .header { display: flex; justify-content: space-between; align-items: center; }
        .btn { padding: 8px 16px; border-radius: 4px; text-decoration: none; display: inline-block; margin: 4px; border: none; cursor: pointer; }
        .btn-green { background: #4CAF50; color: white; }
        .btn-red { background: #f44336; color: white; }
        .btn-blue { background: #2196F3; color: white; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 10px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background-color: #f5f5f5; }
        .favorito { color: gold; }
    </style>
</head>
<body>
    <div class="header">
        <h2>📒 Bienvenido, <?php echo isset($_SESSION['usuario_nombre']) ? $_SESSION['usuario_nombre'] : 'Usuario'; ?></h2>
        <div>
            <a href="contacto_registrar.php" class="btn btn-green">➕ Nuevo Contacto</a>
            <a href="cerrar_sesion.php" class="btn btn-red">Cerrar Sesión</a>
        </div>
    </div>

    <h3>Mis Contactos</h3>

    <?php if (isset($error)): ?>
        <p style="color:red;"><?php echo $error; ?></p>
    <?php endif; ?>

    <?php if (!empty($contactos)): ?>
        <table>
            <tr>
                <th>Nombre</th>
                <th>Correo</th>
                <th>Ciudad</th>
                <th>Favorito</th>
                <th>Acciones</th>
            </tr>
            <?php foreach ($contactos as $c): ?>
            <tr>
                <td><?php echo htmlspecialchars($c['nombre']); ?></td>
                <td><?php echo htmlspecialchars($c['correo']); ?></td>
                <td><?php echo htmlspecialchars($c['ciudad']); ?></td>
                <td><?php echo !empty($c['favorito']) && $c['favorito'] ? '<span class="favorito">⭐</span>' : ''; ?></td>
                <td>
                    <a href="contacto_editar.php?id=<?php echo $c['id']; ?>" class="btn btn-blue">Editar</a>
                    <a href="contacto_eliminar.php?id=<?php echo $c['id']; ?>" class="btn btn-red" onclick="return confirm('¿Eliminar este contacto?')">Eliminar</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
    <?php else: ?>
        <p>No tienes contactos guardados aún.</p>
        <p><a href="contacto_registrar.php" class="btn btn-green">Agrega tu primer contacto</a></p>
    <?php endif; ?>
</body>
</html>
<body>
    <h1>📋 Gestión de Contactos</h1>
    
    <p>
        <a href="panel.php?solo_favoritos=1">⭐ Ver solo favoritos</a> | 
        <a href="panel.php">Ver todos</a> |
        <a href="contacto_registrar.php">➕ Agregar nuevo contacto</a> |
        <a href="logout.php">🚪 Cerrar sesión</a>
    </p>

    <!-- FORMULARIO DE BÚSQUEDA -->
    <form method="GET" action="panel.php" style="margin: 15px 0;">
        <input type="text" name="buscar" placeholder="Buscar por nombre, correo o ciudad..." 
               value="<?php echo htmlspecialchars($buscar); ?>" style="padding: 6px; width: 300px;">
        <button type="submit">🔍 Buscar</button>
        <?php if (!empty($buscar)): ?>
            <a href="panel.php">❌ Limpiar</a>
        <?php endif; ?>
    </form>

    <!-- TABLA DE CONTACTOS -->
    <table border="1" cellpadding="8" cellspacing="0" style="width: 100%; border-collapse: collapse;">
        <thead>
            <tr>
                <th>
                    <a href="panel.php?orden=nombre&direccion=<?php echo $direccion === 'ASC' ? 'DESC' : 'ASC'; ?>&buscar=<?php echo urlencode($buscar); ?><?php echo $solo_favoritos ? '&solo_favoritos=1' : ''; ?>">
                        Nombre <?php echo $orden === 'nombre' ? ($direccion === 'ASC' ? '↑' : '↓') : ''; ?>
                    </a>
                </th>
                <th>Correo</th>
                <th>
                    <a href="panel.php?orden=ciudad&direccion=<?php echo $direccion === 'ASC' ? 'DESC' : 'ASC'; ?>&buscar=<?php echo urlencode($buscar); ?><?php echo $solo_favoritos ? '&solo_favoritos=1' : ''; ?>">
                        Ciudad <?php echo $orden === 'ciudad' ? ($direccion === 'ASC' ? '↑' : '↓') : ''; ?>
                    </a>
                </th>
                <th>Categoría</th>
                <th>
                    <a href="panel.php?orden=fecha_creacion&direccion=<?php echo $direccion === 'ASC' ? 'DESC' : 'ASC'; ?>&buscar=<?php echo urlencode($buscar); ?><?php echo $solo_favoritos ? '&solo_favoritos=1' : ''; ?>">
                        Fecha <?php echo $orden === 'fecha_creacion' ? ($direccion === 'ASC' ? '↑' : '↓') : ''; ?>
                    </a>
                </th>
                <th>Favorito</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php if (count($contactos) > 0): ?>
                <?php foreach ($contactos as $c): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($c['nombre']); ?></td>
                        <td><?php echo htmlspecialchars($c['correo']); ?></td>
                        <td><?php echo htmlspecialchars($c['ciudad']); ?></td>
                        <td><?php echo htmlspecialchars($c['categoria_nombre'] ?? 'Sin categoría'); ?></td>
                        <td><?php echo date('d/m/Y H:i', strtotime($c['fecha_creacion'])); ?></td>
                        <td style="text-align: center;">
                            <?php if ($c['favorito'] === 'Si'): ?>
                                ⭐
                                <a href="toggle_favorito.php?id=<?php echo $c['id']; ?>&volver=panel.php?orden=<?php echo urlencode($orden); ?>&direccion=<?php echo urlencode($direccion); ?>&buscar=<?php echo urlencode($buscar); ?><?php echo $solo_favoritos ? '&solo_favoritos=1' : ''; ?>">Quitar</a>
                            <?php else: ?>
                                ☆
                                <a href="toggle_favorito.php?id=<?php echo $c['id']; ?>&volver=panel.php?orden=<?php echo urlencode($orden); ?>&direccion=<?php echo urlencode($direccion); ?>&buscar=<?php echo urlencode($buscar); ?><?php echo $solo_favoritos ? '&solo_favoritos=1' : ''; ?>">Marcar</a>
                            <?php endif; ?>
                        </td>
                        <td>
                            <a href="contacto_editar.php?id=<?php echo $c['id']; ?>">✏️ Editar</a> |
                            <a href="contacto_eliminar.php?id=<?php echo $c['id']; ?>" onclick="return confirm('¿Eliminar este contacto?')">🗑️ Eliminar</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="7" style="text-align: center; padding: 20px;">
                        <?php echo !empty($buscar) ? "No se encontraron resultados para: <strong>" . htmlspecialchars($buscar) . "</strong>" : "No hay contactos registrados. ¡Agrega el primero!"; ?>
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <p><a href="contacto_registrar.php">➕ Agregar nuevo contacto</a></p>
</body>
</html>