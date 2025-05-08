<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PHP</title>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/php1.css">
</head>

<body>
    <div>
        <?php
        echo "<p>Hola Mundo desde PHP</p>";
        echo "<p>Lista de compras</p>";
        $compras = [
            1 => "Leche",
            2 => "Pan",
            3 => "Huevos",
            4 => "Frutas",
            5 => "Verduras"
        ];
        foreach ($compras as $id => $compra) {
            echo "<input type='checkbox' value='$id'> $compra <br>";
        }
        ?>
    </div>
</body>

</html>