<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Producto</title>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/create_product.css">
</head>

<body>
    <?php
    if (isset($_POST['nombre']) && isset($_POST['precio']) && isset($_POST['cantidad'])) {
        file_put_contents("../data/productos.csv", $_POST['nombre'] . "," . $_POST['precio'] . "," . $_POST['cantidad'] . "\n", FILE_APPEND);

        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    }

    $data = [];
    if (file_exists("../data/productos.csv")) {
        $file = fopen("../data/productos.csv", "r");
        $headers = fgetcsv($file);
        while (($row = fgetcsv($file)) !== false) {
            $data[] = array_combine($headers, $row);
        }
    }
    ?>

    <form action="create_product.php" method="POST">
        <label for="nombre">Nombre del producto:</label>
        <input type="text" name="nombre" id="nombre" required>
        <br>
        <label for="precio">Precio:</label>
        <input type="number" name="precio" id="precio" required>
        <br>
        <label for="cantidad">Cantidad:</label>
        <input type="number" name="cantidad" id="cantidad" required>
        <br>
        <button type="submit" name="hola">Crear Producto</button>
    </form>

    <?php
    if (!empty($data)) {
        echo "<h2>Lista de Productos</h2>";
        echo "<table border='1'>";
        echo "<tr><th>Nombre</th><th>Precio</th><th>Cantidad</th></tr>";
        foreach ($data as $producto) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($producto['nombre']) . "</td>";
            echo "<td>" . htmlspecialchars($producto['precio']) . "</td>";
            echo "<td>" . htmlspecialchars($producto['cantidad']) . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
    ?>
</body>

</html>