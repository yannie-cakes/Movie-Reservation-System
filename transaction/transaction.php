<?php
include('../database_connection.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fname = $_POST['fname'];
    $mname = $_POST['mname'];
    $lname = $_POST['lname'];
    $seats = $_POST['seats']; // Array of seats
    $payment = $_POST['payment'];
    $totalPrice = count($seats) * 250; // Example price calculation
    $change = $payment - $totalPrice;
    $transactionNumber = uniqid(); // Generate a unique transaction number

    // Insert transaction into database
    $empId = 1; // Replace with actual logged-in employee ID
    $stmt = $mysqli->prepare("INSERT INTO TRANSACTIONS (TRANS_NUMBER, TRANS_DUE, TRANS_PAYMENT, TRANS_CHANGE, TRANS_DATE, EMP_ID)
                              VALUES (?, ?, ?, ?, NOW(), ?)");
    $stmt->bind_param("iiiii", $transactionNumber, $totalPrice, $payment, $change, $empId);
    $stmt->execute();

    // Insert seats and tickets into database
    foreach ($seats as $seat) {
        $seatId = $seat; // Customize if needed
        $cinemaId = 1;   // Example cinema ID
        $movieId = 1;    // Example movie ID

        // Reserve seat
        $stmt = $mysqli->prepare("INSERT INTO SEAT (SEAT_ID, SEAT_NUMBER, CIN_ID) VALUES (?, ?, ?)");
        $stmt->bind_param("ssi", $seatId, $seat, $cinemaId);
        $stmt->execute();

        // Add ticket
        $stmt = $mysqli->prepare("INSERT INTO TICKET (TICKET_PRICE, TICKET_QUANTITY, TRANS_NUMBER, MOV_ID, CIN_ID, SEAT_ID) 
                                  VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("iiiiss", $seatPrice, 1, $transactionNumber, $movieId, $cinemaId, $seatId);
        $stmt->execute();
    }

    header("Location: success.html?trans_number=$transactionNumber");
    exit;
}
?>