<?php
$host = 'localhost';
$usuario = 'root';
$contrasena = '';
$base_datos = 'crud2';

$conexion = mysqli_connect($host, $usuario, $contrasena, $base_datos);

if (!$conexion) {
    die('Error de conexión: ' . mysqli_connect_error());
}

// Función para obtener todos los tipos de producto
function obtenerTiposProducto($conexion)
{
    $query = "SELECT * FROM tipos_producto";
    $result = mysqli_query($conexion, $query);
    $tipos_producto = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $tipos_producto[] = $row;
    }
    return $tipos_producto;
}

// Función para obtener la información de un tipo de producto por ID
function obtenerTipoProductoPorID($conexion, $tipo_producto_id)
{
    $query = "SELECT * FROM tipos_producto WHERE id = $tipo_producto_id";
    $result = mysqli_query($conexion, $query);
    return mysqli_fetch_assoc($result);
}

// Función para insertar un nuevo tipo de producto
function agregarTipoProducto($conexion, $nombre)
{
    $nombre = mysqli_real_escape_string($conexion, $nombre);
    $query = "INSERT INTO tipos_producto (nombre) VALUES ('$nombre')";
    mysqli_query($conexion, $query);
}

// Función para actualizar la información de un tipo de producto
function actualizarTipoProducto($conexion, $tipo_producto_id, $nombre)
{
    $nombre = mysqli_real_escape_string($conexion, $nombre);
    $query = "UPDATE tipos_producto SET nombre='$nombre' WHERE id=$tipo_producto_id";
    mysqli_query($conexion, $query);
}

// Manejo de formularios para Tipos de Producto
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['agregar_tipo_producto'])) {
        // Obtener datos del formulario para agregar
        $id_tipo_producto = mysqli_real_escape_string($conexion, $_POST['id_tipo_producto']);
        $nombre_tipo_producto = mysqli_real_escape_string($conexion, $_POST['nombre_tipo_producto']);

        // Verificar si el ID ya existe (solo para agregar)
        $verificar_id_query = "SELECT id FROM tipos_producto WHERE id = $id_tipo_producto";
        $result = mysqli_query($conexion, $verificar_id_query);

        if (mysqli_num_rows($result) > 0) {
            echo "Error: El ID ya existe en la base de datos.";
        } else {
            // Insertar datos
            $query = "INSERT INTO tipos_producto (id, nombre) VALUES ($id_tipo_producto, '$nombre_tipo_producto')";
            
            // Imprimir la consulta SQL para depuración
            echo "Query: $query";
            
            // Ejecutar la consulta
            if (mysqli_query($conexion, $query)) {
                echo "Datos agregados correctamente";
            } else {
                echo "Error al agregar: " . mysqli_error($conexion);
            }
        }
    } elseif (isset($_POST['editar_tipo_producto'])) {
        // Obtener datos del formulario para editar
        $id_tipo_producto_editar = mysqli_real_escape_string($conexion, $_POST['id_tipo_producto']);
        $nombre_tipo_producto_editar = mysqli_real_escape_string($conexion, $_POST['nombre_tipo_producto']);

        // Actualizar datos
        $query_editar = "UPDATE tipos_producto SET nombre = '$nombre_tipo_producto_editar' WHERE id = $id_tipo_producto_editar";

        // Imprimir la consulta SQL para depuración
        echo "Query Editar: $query_editar";

        // Ejecutar la consulta
        if (mysqli_query($conexion, $query_editar)) {
            echo "Datos editados correctamente";
        } else {
            echo "Error al editar: " . mysqli_error($conexion);
        }
    }
}

// Obtener la lista de tipos de producto
$tipos_producto = obtenerTiposProducto($conexion);

// Inicializar variables para el formulario
$id_tipo_producto_editar = '';
$nombre_tipo_producto_editar = '';

// Si se especifica un ID en la URL, obtener la información del tipo de producto
if (isset($_GET['id'])) {
    $tipo_producto_id = $_GET['id'];
    $tipo_producto = obtenerTipoProductoPorID($conexion, $tipo_producto_id);
    $id_tipo_producto_editar = $tipo_producto['id'];
    $nombre_tipo_producto_editar = $tipo_producto['nombre'];
}

// Determinar la acción a realizar (agregar o editar)
$accion_tipo_producto = empty($id_tipo_producto_editar) ? 'agregar' : 'editar';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <title>CRUD Tipos de Producto</title>
</head>
<body>

<div class="container mt-5">

    <a class="btn btn-primary mb-3" href="productos.php">Productos</a>

    <!-- Formulario de edición/agregado -->
    <h2><?php echo ucfirst($accion_tipo_producto); ?> Tipo de Producto</h2>
    <form method="post">
        <div class="form-group">
            <label for="id_tipo_producto">ID:</label>
            <input type="text" class="form-control" id="id_tipo_producto" name="id_tipo_producto" value="<?php echo $id_tipo_producto_editar; ?>">
        </div>

        <div class="form-group">
            <label for="nombre_tipo_producto">Nombre:</label>
            <input type="text" class="form-control" id="nombre_tipo_producto" name="nombre_tipo_producto" value="<?php echo $nombre_tipo_producto_editar; ?>">
        </div>

        <?php if ($accion_tipo_producto == 'agregar') : ?>
            <button type="submit" class="btn btn-success" name="agregar_tipo_producto">Agregar</button>
        <?php elseif ($accion_tipo_producto == 'editar') : ?>
            <button type="submit" class="btn btn-primary" name="editar_tipo_producto">Guardar cambios</button>
        <?php endif; ?>

        <a class="btn btn-secondary" href="tipos_producto.php">Cancelar</a>
    </form>

    <h2 class="mt-5">Tipos de Producto</h2>

    <!-- Mostrar la tabla de tipos de producto -->
    <table class="table table-bordered">
        <thead>
        <tr>
            <th>ID</th>
            <th>Nombre</th>
            <th>Acciones</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($tipos_producto as $row) : ?>
            <tr>
                <td><?php echo $row['id']; ?></td>
                <td><?php echo $row['nombre']; ?></td>
                <td><a class="btn btn-warning" href="?id=<?php echo $row['id']; ?>">Editar</a></td>
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
