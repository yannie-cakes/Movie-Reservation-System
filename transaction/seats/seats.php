<?php
session_start();
include('../database_connection.php');

$errorMessage = null;

if (!isset($_SESSION['EMP_FNAME']) && !isset($_SESSION['EMP_ID'])) {
    header('Location: ../login.php');
    exit();
}

if (isset($_GET['cin_id'])) {
   $_SESSION['cin_id'] = $_GET['cin_id']; // Store CIN_ID in session
} else {
    echo 'CIN_ID is missing.';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Seat Reservation</title>
    <link rel="stylesheet" href="seats.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>
    <nav class="nav-bar">
        <div class="profile">
        <div class="avatar"></div>
        <div class="info">
            <h3 class="emp-name">Employee Name</h3>
            <h4 class="emp-id">31735127</h4>
        </div>
        </div>
        <h4 class="label">Databases</h4>
        <a href="">
        <div class="tab">
            <i class="bi bi-people-fill"></i>
            <h3 class="tab-label">Employees</h3>
        </div>
        </a>
        <a href="">
        <div class="tab">
            <i class="bi bi-bank2"></i>
            <h3 class="tab-label">Transactions</h3>
        </div>
        </a>
        <a href="">
        <div class="tab">
            <i class="bi bi-ticket-perforated-fill"></i>
            <h3 class="tab-label">Tickets</h3>
        </div>
        </a>
        <h4 class="label">Account Options</h4>
        <a href="">
        <div class="tab">
            <i class="bi bi-box-arrow-left"></i>
            <h3 class="tab-label">Log Out</h3>
        </div>
        </a>
    </nav>

    <section class="seat-selection">
        <h2>Seat Selection</h2>
        <div class="screen">SCREEN</div>
        <form class="seats-form" action="../transaction.php?cin_id=<?php echo $_SESSION['cin_id']; ?>  " method="post">

            <div id="seats-container"></div>
            <button type="submit" id="reserve-button">Reserve</button>
        </form>
    </section>
    
    <script>
        const rows = ['A', 'B', 'C', 'D', 'E', 'F'];
        const seatsPerRow = { 'A': 8, 'default': 14 };
        const seatsContainer = document.getElementById('seats-container');
    
        rows.forEach(row => {
            const rowContainer = document.createElement('div');
            rowContainer.classList.add('row');
    
            const seatRow = document.createElement('div');
            seatRow.classList.add('seat-row');
    
            const seatCount = seatsPerRow[row] || seatsPerRow['default'];
            for (let i = 1; i <= seatCount; i++) {
                const seatId = `${row}${i}`;
                const checkbox = document.createElement('input');
                checkbox.type = 'checkbox';
                checkbox.name = 'seats[]';
                checkbox.id = seatId;
                checkbox.value = seatId;
    
                const label = document.createElement('label');
                label.htmlFor = seatId;
                label.textContent = seatId;
    
                seatRow.appendChild(checkbox);
                seatRow.appendChild(label);
            }
    
            rowContainer.appendChild(seatRow);
            seatsContainer.appendChild(rowContainer);
        });        
    </script>
    <script>
        // Retrieve and highlight previously selected seats from localStorage
        document.addEventListener("DOMContentLoaded", () => {
            const selectedSeats = JSON.parse(localStorage.getItem("selectedSeats")) || [];

            selectedSeats.forEach(seatId => {
                const seatCheckbox = document.getElementById(seatId);
                if (seatCheckbox) {
                    seatCheckbox.checked = true;
                }
            });

            // Handle seat selection
            document.getElementById("seats-container").addEventListener("change", (event) => {
                const seat = event.target;
                const selectedSeats = JSON.parse(localStorage.getItem("selectedSeats")) || [];

                if (seat.type === "checkbox") {
                    if (seat.checked) {
                        selectedSeats.push(seat.value);
                    } else {
                        const index = selectedSeats.indexOf(seat.value);
                        if (index !== -1) {
                            selectedSeats.splice(index, 1);
                        }
                    }
                }

                // Save selected seats to localStorage
                localStorage.setItem("selectedSeats", JSON.stringify(selectedSeats));
            });
        });
    </script>
</body>
</html>
