<?php
session_start();
include('../../../database_connection.php');

$errorMessage = null;

if (!isset($_SESSION['EMP_FNAME']) && !isset($_SESSION['EMP_ID'])) {
    header('Location: ../login.php');
    exit();
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
                    <h3 class="tab-label">Transactions</div>
                </div>
            </a>

            <a href="">
                <div class="tab">
                    <i class="bi bi-ticket-perforated-fill"></i>
                    <h3 class="tab-label">Tickets</h3>
                </div>
            </a>

        <h4 class="label">Account Options</h4>
        <a href="../../login/logout.php">
            <div class="tab">
                <i class="bi bi-box-arrow-left"></i>
                <h3 class="tab-label">Log Out</h3>
            </div>
        </a>
    </nav>
    
    <div class="transaction-box">
        <h1 class="title">TRANSACTIONS</h1>
        <form action="" class="trans-form">
            <div class="container">
                <div class="customer">
                    <label for="fname">Customer ID</label>
                    <p>Customer ID #</p>
                </div>

                <div class="payment-details">
                    <label for="seats">Seats</label>
                    <table name="seats">
                        <td>S2</td>
                        <td>S2</td>
                    </table>

                    <label for="price">Total Price</label>
                    <p name="price">price</p>

                    <label for="payment">Total Payment</label>
                    <input type="number" name="payment" placeholder=" Enter payment." required>
                </div>  
            </div>
            <input type="submit" formaction="" value="Confirm">
        </form>
    </div>

</body>
</html>