<?php
require "connection.php";

class Boleto
{
    private $precioBase = 100;

    private $fecha_compra;

    private $conn;

    public function __construct(
        private $artista,
        private $tipo,
        private $cantidad,
        private bool $seguro,
        private Cliente $cliente
    ) {
        $this->fecha_compra = date("Y-m-d H:i");
        $this->conn = new DatabaseConnection();
    }

    public function guardar(): int
    {
        // CREATE TABLE Boletos (
        //     id SERIAL PRIMARY KEY,
        //     cliente VARCHAR(255) NOT NULL,
        //     email VARCHAR(255) NOT NULL,
        //     artista VARCHAR(255) NOT NULL,
        //     cantidad INT NOT NULL,
        //     tipo VARCHAR(255) NOT NULL,
        //     fecha_compra TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        // );

        // ALTER TABLE Boletos
        // ADD seguro BOOLEAN DEFAULT FALSE

        // Returning id
        $query = "INSERT INTO Boletos (cliente, email, artista, cantidad, tipo, fecha_compra, seguro) VALUES ($1, $2, $3, $4, $5, $6, $7) RETURNING id";
        $result = $this->conn->executeQuery($query, [
            $this->cliente->getNombre(),
            $this->cliente->getEmail(),
            $this->artista,
            $this->cantidad,
            $this->tipo,
            $this->fecha_compra,
            $this->seguro
        ]);
        if ($result) {
            return $result[0]['id'];
        }
        return 0;
    }

    public function getPrecioTotal()
    {
        $precio = $this->precioBase;
        if ($this->tipo === "VIP") {
            $precio *= 2;
        } elseif ($this->tipo === "Premium") {
            $precio *= 1.5;
        }
        $precioTotal = $precio * $this->cantidad;
        if ($this->seguro) {
            $precioTotal += 100 * $this->cantidad;
        }
        return $precioTotal;
    }

    public function getartista()
    {
        return $this->artista;
    }

    public function getTipo()
    {
        return $this->tipo;
    }

    public function getCantidad()
    {
        return $this->cantidad;
    }

    public function getFechaCompra()
    {
        return $this->fecha_compra;
    }

    public function getSeguro()
    {
        return $this->seguro;
    }

    public function getCliente()
    {
        return $this->cliente;
    }

    public static function getBoletoById($id): ?Boleto
    {
        $conn = new DatabaseConnection();
        $query = "SELECT * FROM Boletos WHERE id = $1";
        $params = [$id];
        $result = $conn->executeQuery($query, $params);
        // var_dump($result);
        // die();
        if ($result) {
            $result = $result[0];
            return new Boleto(
                $result['artista'],
                $result['tipo'],
                $result['cantidad'],
                $result['seguro'],
                new Cliente($result['cliente'], $result['email'])
            );
        }
        return null;
    }

    public function getPrecioSinSeguro()
    {
        $precio = $this->precioBase;
        if ($this->tipo === "VIP") {
            $precio *= 2;
        } elseif ($this->tipo === "Premium") {
            $precio *= 1.5;
        }
        return $precio * $this->cantidad;
    }

    public function getCostoSeguro()
    {
        return $this->seguro ? (100 * $this->cantidad) : 0;
    }
}

class Cliente
{
    public function __construct(
        private $nombre,
        private $email
    ) {}

    public function getNombre()
    {
        return $this->nombre;
    }

    public function getEmail()
    {
        return $this->email;
    }
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = $_POST['nombre'];
    $email = $_POST['email'];
    $artista = $_POST['artista'];
    $tipo = $_POST['tipo'];
    $cantidad = $_POST['cantidad'];
    $seguro = $_POST['seguro'];

    $cliente = new Cliente($nombre, $email);
    $boleto = new Boleto($artista, $tipo, $cantidad, $seguro, $cliente);
    $id = $boleto->guardar();

    if ($id == 0) {
        $error = "Error al guardar el boleto.";
        exit();
    }

    header("Location: " . $_SERVER['PHP_SELF'] . "?id=" . $id);
    exit();
} elseif (isset($_GET['id'])) {
    $boleto = Boleto::getBoletoById($_GET['id']);
    $cliente = $boleto->getCliente();
} else {
    $boleto = null;
    $cliente = null;
}

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Boleto Comprado</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        /* Fuente Inter para una mejor legibilidad */
        body {
            font-family: "Inter", sans-serif;
        }
    </style>
</head>

<body class="bg-gradient-to-br from-blue-600 to-teal-800 min-h-screen flex items-center justify-center p-4">
    <div class="bg-white p-8 rounded-xl shadow-2xl w-full max-w-md border border-gray-200 text-gray-800">
        <?php
        if (isset($boleto) && isset($cliente)) {
        ?>
            <h1 class="text-3xl font-bold text-center text-gray-800 mb-6">¡Gracias por tu compra, <?= htmlspecialchars($cliente->getNombre()); ?>!</h1>
            <p class="text-lg text-gray-700 mb-4 text-center">Detalles de tu compra:</p>
            <ul class="space-y-2 mb-6 text-gray-700">
                <li class="flex justify-between items-center bg-gray-50 p-3 rounded-lg shadow-sm">
                    <span class="font-semibold">Artista:</span>
                    <span><?= htmlspecialchars($boleto->getartista()); ?></span>
                </li>
                <li class="flex justify-between items-center bg-gray-50 p-3 rounded-lg shadow-sm">
                    <span class="font-semibold">Tipo de Boleto:</span>
                    <span><?= htmlspecialchars($boleto->getTipo()); ?></span>
                </li>
                <li class="flex justify-between items-center bg-gray-50 p-3 rounded-lg shadow-sm">
                    <span class="font-semibold">Cantidad:</span>
                    <span><?= htmlspecialchars($boleto->getCantidad()); ?></span>
                </li>
                <li class="flex justify-between items-center bg-gray-50 p-3 rounded-lg shadow-sm">
                    <span class="font-semibold">Fecha de Compra:</span>
                    <span><?= htmlspecialchars($boleto->getFechaCompra()); ?></span>
                </li>
            </ul>

            <div class="border-t border-gray-300 pt-4 mt-4 space-y-2">
                <div class="flex justify-between text-lg font-medium text-gray-700">
                    <span>Subtotal:</span>
                    <span>$<?= number_format($boleto->getPrecioSinSeguro(), 2); ?></span>
                </div>
                <div class="flex justify-between text-lg font-medium text-gray-700">
                    <span>Costo del Seguro:</span>
                    <span>$<?= number_format($boleto->getCostoSeguro(), 2); ?></span>
                </div>
                <div class="flex justify-between text-2xl font-bold text-gray-900 border-t border-gray-400 pt-2">
                    <span>Total:</span>
                    <span>$<?= number_format($boleto->getPrecioTotal(), 2); ?></span>
                </div>
            </div>

            <p class="text-md text-gray-600 mt-6 text-center">Recibirás un correo de confirmación en <span class="font-semibold text-purple-700"><?= htmlspecialchars($cliente->getemail()); ?></span>.</p>
        <?php
        } elseif (isset($error)) {
        ?>
            <p class="text-xl text-center text-red-600">Error: <?= htmlspecialchars($error); ?></p>
        <?php
        } else {
        ?>
            <p class="text-xl text-center text-red-600">No se han recibido datos de compra válidos.</p>
        <?php
        }
        ?>
    </div>
</body>

</html>