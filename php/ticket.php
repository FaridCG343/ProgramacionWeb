<?php
function getLastTicketNumber(): int
{
    $file = fopen("../data/tickets.csv", "r");
    $lastTicketNumber = 0;
    $headers = fgetcsv($file);
    while (($row = fgetcsv($file)) !== false) {
        $row = array_combine($headers, $row);
        if ($row['id'] > $lastTicketNumber) {
            $lastTicketNumber = $row['id'];
        }
    }
    fclose($file);
    return $lastTicketNumber;
}

function getTicketData(int $ticketNumber): array
{
    $file = fopen("../data/tickets.csv", "r");
    $ticketData = [];
    $headers = fgetcsv($file);
    while (($row = fgetcsv($file)) !== false) {
        $row = array_combine($headers, $row);
        if ($row['id'] == $ticketNumber) {
            $ticketData = $row;
            break;
        }
    }
    fclose($file);
    return $ticketData;
}

if (!empty($_POST['cliente']) && (!empty($_POST['bebidas_calientes']) || !empty($_POST['bebidas_frias']) || !empty($_POST['alimentos']))) {
    $lastTicketNumber = getLastTicketNumber();
    $ticket = [
        'id' => $lastTicketNumber + 1,
        'cliente' => $_POST['cliente'],
        'bebida_caliente' => $_POST['bebidas_calientes'] ?? '',
        'bebida_fria' => $_POST['bebidas_frias'] ?? '',
        'alimento' => $_POST['alimentos'] ?? '',
        'comentarios' => $_POST['comentarios'] ?? '',
        'fecha' => date('Y-m-d H:i:s'),
    ];
    $ticketString = implode(',', $ticket) . "\n";
    file_put_contents("../data/tickets.csv", $ticketString, FILE_APPEND);
    header("Location: " . $_SERVER['PHP_SELF'] . "?ticket=" . $ticket['id']); // Esta parte sirve para no mandar otra vez el formulario al recargar la página
    exit();
} elseif (!empty($_GET['ticket'])) {
    $ticket = getTicketData($_GET['ticket']);
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ticket de Venta</title>
    <style>
        body {
            font-family: 'Georgia', serif;
            margin: 20px;
            padding: 20px;
            background-color: #f4f4f4;
            color: #333;
        }

        .ticket {
            max-width: 400px;
            margin: 0 auto;
            background: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border: 2px solid #D2B48C;
        }

        h2 {
            text-align: center;
            color: #4E342E;
            margin-bottom: 20px;
        }

        .info {
            margin-bottom: 10px;
            padding-bottom: 10px;
            border-bottom: 1px dashed #ccc;
        }

        .info strong {
            font-weight: bold;
            color: #3E2723;
        }

        .details {
            margin-top: 20px;
        }

        .details p {
            margin: 5px 0;
        }

        .comments {
            margin-top: 15px;
            padding-top: 10px;
            border-top: 1px dashed #ccc;
        }

        .comments strong {
            font-weight: bold;
            color: #3E2723;
            display: block;
            margin-bottom: 5px;
        }

        .thanks {
            text-align: center;
            margin-top: 20px;
            font-style: italic;
            color: #795548;
        }
    </style>
</head>

<body>
    <div class="ticket">
        <h2>Ticket de Venta</h2>
        <div class="info">
            <strong>ID del Ticket:</strong> <?php echo $ticket['id']; ?><br>
            <strong>Cliente:</strong> <?php echo htmlspecialchars($ticket['cliente']); ?><br>
            <strong>Fecha:</strong> <?php echo $ticket['fecha']; ?>
        </div>
        <div class="details">
            <?php if (!empty($ticket['bebida_caliente'])): ?>
                <p><strong>Bebida Caliente:</strong> <?php echo htmlspecialchars($ticket['bebida_caliente']); ?></p>
            <?php endif; ?>
            <?php if (!empty($ticket['bebida_fria'])): ?>
                <p><strong>Bebida Fría:</strong> <?php echo htmlspecialchars($ticket['bebida_fria']); ?></p>
            <?php endif; ?>
            <?php if (!empty($ticket['alimento'])): ?>
                <p><strong>Alimento:</strong> <?php echo htmlspecialchars($ticket['alimento']); ?></p>
            <?php endif; ?>
        </div>
        <?php if (!empty($ticket['comentarios'])): ?>
            <div class="comments">
                <strong>Comentarios:</strong>
                <p><?php echo nl2br(htmlspecialchars($ticket['comentarios'])); ?></p>
            </div>
        <?php endif; ?>
        <p class="thanks">¡Gracias por tu compra!</p>
    </div>
</body>

</html>