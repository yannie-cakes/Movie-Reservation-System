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
    <title>Transaction</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="trans_styles.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&family=Varela+Round&display=swap" rel="stylesheet">
</head>
<body>
    <nav class="nav-bar">
        <div class="profile">
            <div class="avatar"></div>
            <div class="info">
            <h3 class="emp-name">
                        <?php
                        echo htmlspecialchars($_SESSION['EMP_FNAME']);
                        ?>
                    </h3>
                    <h4 class="emp-id"><?php
                        echo htmlspecialchars($_SESSION['EMP_LNAME']);
                        ?>
                    </h4>
            </div>  
        </div>
        <h4 class="label">Actions</h4>
            <a href="../main/main.php ">
                <div class="tab">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-tv" viewBox="0 0 16 16">
                        <path d="M2.5 13.5A.5.5 0 0 1 3 13h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5M13.991 3l.024.001a1.5 1.5 0 0 1 .538.143.76.76 0 0 1 .302.254c.067.1.145.277.145.602v5.991l-.001.024a1.5 1.5 0 0 1-.143.538.76.76 0 0 1-.254.302c-.1.067-.277.145-.602.145H2.009l-.024-.001a1.5 1.5 0 0 1-.538-.143.76.76 0 0 1-.302-.254C1.078 10.502 1 10.325 1 10V4.009l.001-.024a1.5 1.5 0 0 1 .143-.538.76.76 0 0 1 .254-.302C1.498 3.078 1.675 3 2 3zM14 2H2C0 2 0 4 0 4v6c0 2 2 2 2 2h12c2 0 2-2 2-2V4c0-2-2-2-2-2"/>
                      </svg>
                    <h3 class="tab-label">Movies</h3>
                </div>
            </a>

        <h4 class="label">Account Options</h4>
        <a href="../login/logout.php">
            <div class="tab">
                <i class="bi bi-box-arrow-left"></i>
                <h3 class="tab-label">Log Out</h3>
            </div>
        </a>
    </nav>
    
    <div class="transaction-box">
        <h1 class="title">TRANSACTIONS</h1>
        <form action="../admin/transactions/trans_database.php" method="post" class="trans-form">
            <div class="container">
                <div class="customer">
                    <label for="fname">Cin ID</label>
                    <p><?php echo htmlspecialchars($_SESSION['cin_id'])?></p>
                </div>
                <div class="payment-details">
                    <label for="seats">Seats</label>
                    <table id="seats-table" border="1">
                        <!-- Dynamically populated rows -->
                        <button type="button" class="reserve-seats-btn" onclick="window.location.href='seats/seats.php?cin_id=<?php echo $_SESSION['cin_id']; ?>'">Reserve Seats</button>
                        </table>
                    <br>
                    <label for="price">Total Price</label>
                    <p id="total-price">0</p>
                    <br>
                    <label for="payment">Total Payment</label>
                    <input type="number" name="payment" placeholder="Enter payment..." required>
                    <br>
                    <label for="change">Total Change</label>
                    <p name="change"></p>
                </div>
            </div>
            <input type="submit" value="Confirm">
        </form>
     
        <script>
            document.addEventListener("DOMContentLoaded", () => {
                const seats = JSON.parse(localStorage.getItem("selectedSeats")) || [];
                const seatsTable = document.getElementById("seats-table");

                // Populate seats in the table
                seats.forEach((seat) => {
                    const row = document.createElement("tr");
                    row.innerHTML = `<td>${seat}</td>`;
                    seatsTable.appendChild(row);
                });

                // Calculate total price
                const seatPrice = 150; // Example price
                document.getElementById("total-price").textContent = seats.length * seatPrice;

                // Add seats to form for server-side processing
                const form = document.querySelector(".trans-form");
                seats.forEach((seat) => {
                    const input = document.createElement("input");
                    input.type = "hidden";
                    input.name = "selectedSeats[]";
                    input.value = seat;
                    form.appendChild(input);
                });
            });
        
            // Fetch the latest customer ID
            fetch('fetch_customer_id.php')
                .then(response => response.json())
                .then(data => {
                    const cusId = data.cus_id; // Display this where appropriate
                    console.log('Customer ID:', cusId);
                })
                .catch(error => console.error('Error fetching customer ID:', error));
        </script>
    </div>
</body>
</html>
