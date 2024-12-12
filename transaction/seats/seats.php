<?php
// Include the database connection
include('../database_connection.php');

if (isset($_GET['transaction_number'])) {
    $transactionNumber = $_GET['transaction_number'];
} else {
    echo "<p>Invalid transaction number.</p>";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['seats'])) {
    $selectedSeats = $_POST['seats']; 
    $cinemaId = 1; 

    // Array to hold successfully reserved seat IDs
    $reservedSeats = [];

    foreach ($selectedSeats as $seatNumber) {
        // Generate a unique SEAT_ID
        $seatId = $cinemaId . $seatNumber;  

        // Check if the seat is already reserved
        $checkSeatQuery = "SELECT SEAT_ID FROM SEAT WHERE SEAT_ID = ?";
        $stmt = $mysqli->prepare($checkSeatQuery);
        $stmt->bind_param("s", $seatId);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            // Seat is already reserved
            echo "<p>Seat $seatNumber is already reserved!</p>";
        } else {
            // Reserve the seat
            $insertSeatQuery = "INSERT INTO SEAT (SEAT_ID, SEAT_NUMBER, CIN_ID) VALUES (?, ?, ?)";
            $stmt = $mysqli->prepare($insertSeatQuery);
            $stmt->bind_param("ssi", $seatId, $seatNumber, $cinemaId);

            if ($stmt->execute()) {
                echo "<p>Seat $seatNumber reserved successfully!</p>";

                // Insert into TICKET now that the transaction is successfully inserted
                $insertTicketQuery = "INSERT INTO TICKET (TICKET_PRICE, TICKET_QUANTITY, TRANS_NUMBER, MOV_ID, CIN_ID, SEAT_ID) 
                                      VALUES (?, ?, ?, ?, ?, ?)";
                $stmt = $mysqli->prepare($insertTicketQuery);
                $stmt->bind_param("iiiiss", 250, 1, $transactionNumber, 1 /* movieId */, 
                                  $cinemaId, $seatId);

                if ($stmt->execute()) {
                    echo "<p>Seat $seatNumber added to the transaction.</p>";
                    // Add seat ID to reserved seats array for redirection
                    array_push($reservedSeats, urlencode($seatId));
                } else {
                    echo "<p>Failed to add Seat $seatNumber to the transaction.</p>";
                }
            } else {
                echo "<p>Failed to reserve seat $seatNumber.</p>";
            }
        }
    }

    // Redirect back to the transactions page with the reserved seats
    $reservedSeatsString = implode(',', $reservedSeats);
    header("Location: transactions.php?reserved_seats=$reservedSeatsString&transaction_number=$transactionNumber");
    exit();
}