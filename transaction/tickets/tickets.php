<?php
include("../../database_connection.php");
session_start();

// Check if the required parameters are passed in the URL
if (isset($_GET['seats']) && isset($_GET['payment']) && isset($_GET['change']) && isset($_GET['total_due']) && isset($_GET['trans_number'])) {
    // Get the values of the parameters
    $seats = $_GET['seats'];
    $payment = $_GET['payment'];
    $change = $_GET['change'];
    $totalDue = $_GET['total_due'];
    $transNumber = $_GET['trans_number'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ticket Details</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        /* Style the ticket details page */
        .ticket-details {
            margin: 20px;
            font-family: 'Montserrat', sans-serif;
            font-size: 18px;
        }
        .detail {
            margin: 10px 0;
        }
        .label {
            font-weight: bold;
        }
    </style>
</head>
<body>

<div class="ticket-details">
    <h1>Transaction Details</h1>
    
    <div class="detail"> 
        <span class="label">Transaction Number:</span>
        <span>#<?php echo htmlspecialchars($transNumber); ?></span>
    </div>
    <div class="detail">
        <span class="label">Seats:</span>
        <span><?php echo htmlspecialchars($seats); ?></span>
    </div>
    <div class="detail">
        <span class="label">Total Due:</span>
        <span>$<?php echo number_format($totalDue, 2); ?></span>
    </div>
    <div class="detail">
        <span class="label">Payment:</span>
        <span>$<?php echo number_format($payment, 2); ?></span>
    </div>
    <div class="detail">
        <span class="label">Change:</span>
        <span>$<?php echo number_format($change, 2); ?></span>
    </div>
</div>

<a href="../../main/main.php">Back to Home</a>

</body>
</html>
