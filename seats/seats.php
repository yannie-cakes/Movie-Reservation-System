<?php
include('../database_connection.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $selectedSeats = $_POST['seats']; // Assumes 'seats' is an array of selected seat values

    $cus_number = 12345; // Example customer ID, should be dynamically assigned
    $trans_number = rand(1000, 9999);

    foreach ($selectedSeats as $seat) {
        $sql = "SELECT SEAT_ID, CIN_ID FROM SEAT WHERE SEAT_NUMBER = '$seat' LIMIT 1";
        $result = $conn->query($sql);
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $seat_id = $row['SEAT_ID'];
            $cinema_id = $row['CIN_ID'];
            
            $insertTicketSQL = "INSERT INTO TICKET (TRANS_NUMBER, MOV_ID, CIN_ID, SEAT_ID) 
                                VALUES ('$trans_number', 1, '$cinema_id', '$seat_id')";
            if ($conn->query($insertTicketSQL) === TRUE) {
                echo "Seat $seat reserved successfully.";
            } else {
                echo "Error: " . $insertTicketSQL . "<br>" . $mysqli->error;
            }
        } else {
            echo "Seat $seat is not available.";
        }
    }
}

$mysqli->close();

?>