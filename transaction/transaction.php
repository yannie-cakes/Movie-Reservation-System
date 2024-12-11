<?php 

include("../database_connection.php");

// Fetch transaction number
$transactionNumber = $_GET['trans_number'] ?? 0;

// Fetch reserved tickets for the transaction
$ticketsQuery = "SELECT TICKET.TICKET_ID, TICKET.TICKET_PRICE, TICKET.SEAT_ID, SEAT.SEAT_NUMBER 
                 FROM TICKET 
                 JOIN SEAT ON TICKET.SEAT_ID = SEAT.SEAT_ID 
                 WHERE TICKET.TRANS_NUMBER = ?";
$stmt = $mysqli->prepare($ticketsQuery);
$stmt->bind_param("i", $transactionNumber);
$stmt->execute();
$result = $stmt->get_result();

// Handle finalization
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $transactionNumber = $_POST['trans_number'];

    // Additional finalization logic can be added here (e.g., mark the transaction as finalized)
    echo "<h1>Transaction Finalized</h1>";
    echo "<p>Transaction $transactionNumber has been successfully finalized!</p>";
    echo "<a href='seats.php'>Reserve More Seats</a>";

    $mysqli->close();
    exit; // Stop further processing
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transaction</title>
</head>
<body>
    <h1>Finalize Transaction</h1>
    <table border="1">
        <thead>
            <tr>
                <th>Ticket ID</th>
                <th>Seat Number</th>
                <th>Ticket Price</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()) { ?>
                <tr>
                    <td><?= $row['TICKET_ID']; ?></td>
                    <td><?= $row['SEAT_NUMBER']; ?></td>
                    <td><?= $row['TICKET_PRICE']; ?></td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
    <form action="transaction.php" method="post">
        <input type="hidden" name="trans_number" value="<?= $transactionNumber; ?>">
        <button type="submit">Finalize Transaction</button>
    </form>
</body>
</html>