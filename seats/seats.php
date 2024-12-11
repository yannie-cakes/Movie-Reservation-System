<?php
// Include the database connection
include('../database_connection.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['seats'])) {
    $selectedSeats = $_POST['seats']; // Array of selected seat numbers
    $cinemaId = 1; // You can dynamically fetch this based on user input or session
    $transactionNumber = 123; // Replace this with dynamically generated transaction numbers

    $allReserved = true; // Flag to track if all seats are successfully reserved

    foreach ($selectedSeats as $seatNumber) {
        // Generate a unique SEAT_ID
        $seatId = $cinemaId . $seatNumber; // Combine cinema ID and seat number for uniqueness

        // Check if the seat is already reserved
        $checkSeatQuery = "SELECT SEAT_ID FROM SEAT WHERE SEAT_ID = ?";
        $stmt = $mysqli->prepare($checkSeatQuery);
        $stmt->bind_param("s", $seatId);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            // Seat is already reserved
            echo "<p>Seat $seatNumber is already reserved!</p>";
            $allReserved = false;
        } else {
            // Reserve the seat
            $insertSeatQuery = "INSERT INTO SEAT (SEAT_ID, SEAT_NUMBER, CIN_ID) VALUES (?, ?, ?)";
            $stmt = $mysqli->prepare($insertSeatQuery);
            $stmt->bind_param("ssi", $seatId, $seatNumber, $cinemaId);

            if ($stmt->execute()) {
                echo "<p>Seat $seatNumber reserved successfully!</p>";

                // Temporarily reserve the seat in TICKET table
                $ticketPrice = 250; // Example ticket price
                $ticketQuantity = 1;
                $movieId = 1; // Replace with the actual movie ID

                // Insert into TICKET without specifying TICKET_ID, as it's auto-incremented
                $insertTicketQuery = "INSERT INTO TICKET (TICKET_PRICE, TICKET_QUANTITY, TRANS_NUMBER, MOV_ID, CIN_ID, SEAT_ID) 
                                    VALUES (?, ?, ?, ?, ?, ?)";
                $stmt = $mysqli->prepare($insertTicketQuery);
                $stmt->bind_param("iiiiss", $ticketPrice, $ticketQuantity, $transactionNumber, $movieId, $cinemaId, $seatId);

                if ($stmt->execute()) {
                    echo "<p>Seat $seatNumber added to the transaction.</p>";
                } else {
                    echo "<p>Failed to add Seat $seatNumber to the transaction.</p>";
                    $allReserved = false;
                }
            } else {
                echo "<p>Failed to reserve seat $seatNumber.</p>";
                $allReserved = false;
            }
        }
    }

    // Redirect to transaction page if all seats are reserved
    if ($allReserved) {
        header("Location: transaction.php?trans_number=$transactionNumber");
        exit;
    }
}

$mysqli->close();
?>
