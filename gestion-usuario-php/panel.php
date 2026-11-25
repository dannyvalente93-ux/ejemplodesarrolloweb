<?php
require 'config.php';

// ============== FILTROS Y PARÁMETROS ==============
$buscar = isset($_GET['buscar']) ? trim($_GET['buscar']) : '';
$solo_favoritos = isset($_GET['solo_favoritos']) ? true : false;

$orden_permitido = ['nombre', 'ciudad', 'fecha_creacion'];
$orden = isset($_GET['orden']) && in_array($_GET['orden'], $orden_permitido) ? $_GET['orden'] : 'nombre';
$direccion = isset($_GET['direccion']) && strtoupper($_GET['direccion']) === 'DESC' ? 'DESC' : 'ASC';

// ============== CONSULTA ==============
$sql = "SELECT c.*, cat.nombre as categoria_nombre 
        FROM contactos c
        LEFT JOIN categorias cat ON c.categoria_id = cat.id";

$condiciones = [];
$parametros = [];

if (!empty($buscar)) {
    $condiciones[] = "(c.nombre LIKE :buscar OR c.correo LIKE :buscar OR c.ciudad LIKE :buscar)";
    $parametros[':buscar'] = "%$buscar%";
}

if ($solo_favoritos) {
    $condiciones[] = "c.favorito = 'Si'";
}

if (!empty($condiciones)) {
    $sql .= " WHERE " . implode(" AND ", $condiciones);
}

$sql .= " ORDER BY $orden $direccion";

$stmt = $conexion->prepare($sql);
foreach ($parametros as $clave => $valor) {
    $stmt->bindValue($clave, $valor, PDO::PARAM_STR);
}
$stmt->execute();
$contactos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel de Contactos</title>
</head>
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