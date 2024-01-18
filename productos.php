<?php
$host = 'localhost';
$usuario = 'root';
$contrasena = '';
$base_datos = 'crud2';

$conexion = mysqli_connect($host, $usuario, $contrasena, $base_datos);

if (!$conexion) {
    die('Error de conexión: ' . mysqli_connect_error());
}

// Función para obtener todos los productos
function obtenerProductos($conexion)
{
    $query = "SELECT p.id, p.nombre, p.cantidad, tp.nombre as tipo_producto FROM productos p
              JOIN tipos_producto tp ON p.tipo_producto_id = tp.id";
    $result = mysqli_query($conexion, $query);
    $productos = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $productos[] = $row;
    }
    return $productos;
}

// Función para obtener la información de un producto por ID
function obtenerProductoPorID($conexion, $producto_id)
{
    $query = "SELECT p.id, p.nombre, p.cantidad, p.tipo_producto_id, tp.nombre as tipo_producto 
              FROM productos p
              JOIN tipos_producto tp ON p.tipo_producto_id = tp.id
              WHERE p.id = $producto_id";
    $result = mysqli_query($conexion, $query);
    return mysqli_fetch_assoc($result);
}

// Función para insertar un nuevo producto
function agregarProducto($conexion, $id, $nombre, $cantidad, $tipo_producto_id)
{
    $nombre = mysqli_real_escape_string($conexion, $nombre);
    $query = "INSERT INTO productos (id, nombre, cantidad, tipo_producto_id) VALUES ($id, '$nombre', $cantidad, $tipo_producto_id)";
    mysqli_query($conexion, $query);
}

// Función para actualizar la información de un producto
function actualizarProducto($conexion, $producto_id, $nombre, $cantidad, $tipo_producto_id)
{
    $nombre = mysqli_real_escape_string($conexion, $nombre);
    $query = "UPDATE productos SET nombre='$nombre', cantidad=$cantidad, tipo_producto_id=$tipo_producto_id WHERE id=$producto_id";
    mysqli_query($conexion, $query);
}

// Función para eliminar un producto
function eliminarProducto($conexion, $producto_id)
{
    $query = "DELETE FROM productos WHERE id = $producto_id";
    mysqli_query($conexion, $query);
}

// Manejo de formularios
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['agregar'])) {
        // Agregar nuevo producto
        // Aquí deberías proporcionar un valor único para $id
        $id = $_POST['id']; // Asegúrate de validar y garantizar que $id sea único
        agregarProducto($conexion, $id, $_POST['nombre'], $_POST['cantidad'], $_POST['tipo_producto_id']);
    } elseif (isset($_POST['editar'])) {
        // Editar producto existente
        actualizarProducto($conexion, $_POST['id'], $_POST['nombre'], $_POST['cantidad'], $_POST['tipo_producto_id']);
    } elseif (isset($_POST['eliminar'])) {
        // Eliminar producto
        eliminarProducto($conexion, $_POST['id']);
    }
}

// Obtener la lista de productos
$productos = obtenerProductos($conexion);

// Inicializar variables para el formulario
$accion = 'agregar';
$producto = ['id' => '', 'nombre' => '', 'cantidad' => '', 'tipo_producto_id' => ''];

// Si se especifica un ID en la URL, obtener la información del producto
if (isset($_GET['id'])) {
    $producto_id = $_GET['id'];
    $producto = obtenerProductoPorID($conexion, $producto_id);
    $accion = 'editar';
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <title>CRUD Productos</title>
</head>
<body>
<div class="container mt-5">

    <a class="btn btn-primary mb-3" href="tipos_producto.php">Tipos de Productos</a>

    <!-- Formulario de edición/agregado/eliminado -->
    <h2><?php echo ucfirst($accion); ?> Producto</h2>
    <form action="productos.php" method="post">
        <div class="form-group">
            <label for="id">ID:</label>
            <input type="text" class="form-control" id="id" name="id" value="<?php echo $producto['id']; ?>">
        </div>

        <div class="form-group">
            <label for="nombre">Nombre:</label>
            <input type="text" class="form-control" id="nombre" name="nombre" value="<?php echo $producto['nombre']; ?>">
        </div>

        <div class="form-group">
            <label for="cantidad">Cantidad:</label>
            <input type="text" class="form-control" id="cantidad" name="cantidad" value="<?php echo $producto['cantidad']; ?>">
        </div>

        <div class="form-group">
            <label for="tipo_producto_id">Tipo de Producto:</label>
            <select class="form-control" id="tipo_producto_id" name="tipo_producto_id">
                <?php
                // Obtener la lista de tipos de producto
                $query_tipos_producto = "SELECT * FROM tipos_producto";
                $result_tipos_producto = mysqli_query($conexion, $query_tipos_producto);

                // Mostrar opciones en el menú desplegable
                while ($tipo = mysqli_fetch_assoc($result_tipos_producto)) {
                    $selected = ($tipo['id'] == $producto['tipo_producto_id']) ? 'selected' : '';
                    echo "<option value='{$tipo['id']}' {$selected}>{$tipo['nombre']}</option>";
                }
                ?>
            </select>
        </div>

        <?php if ($accion == 'agregar') : ?>
            <button type="submit" class="btn btn-success" name="agregar">Agregar</button>
        <?php elseif ($accion == 'editar') : ?>
            <button type="submit" class="btn btn-primary" name="editar">Guardar cambios</button>
        <?php endif; ?>

        <a class="btn btn-secondary" href="productos.php">Cancelar</a>
    </form>

    <h2 class="mt-5">Productos</h2>

    <!-- Mostrar la tabla de productos -->
    <table class="table table-bordered">
        <thead>
        <tr>
            <th>ID</th>
            <th>Nombre</th>
            <th>Cantidad</th>
            <th>Tipo de Producto</th>
            <th>Acciones</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($productos as $row) : ?>
            <tr>
                <td><?php echo $row['id']; ?></td>
                <td><?php echo $row['nombre']; ?></td>
                <td><?php echo $row['cantidad']; ?></td>
                <td><?php echo $row['tipo_producto']; ?></td>
                <td>
                    <a class="btn btn-warning" href="?id=<?php echo $row['id']; ?>">Editar</a>
                    <form method="post" style="display:inline;">
                        <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                        <button type="submit" class="btn btn-danger" name="eliminar">Eliminar</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.1/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>