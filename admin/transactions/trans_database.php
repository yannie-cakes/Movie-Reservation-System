<?php
session_start();
include('../../database_connection.php');

$errorMessage = null;
$updateMessage = null;


if (isset($_SESSION['cin_id'])) {
    $cin_id = $_SESSION['cin_id'];
} else {
    echo 'CIN_ID session is missing.';
}

// Ensure the employee is logged in
// Inside the POST block
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Collect data from the form
    $selectedSeats = $_POST['selectedSeats'];  // Array of selected seats
    $payment = $_POST['payment'];

    // Validate payment amount
    if ($payment <= 0) {
        $updateMessage = "Payment must be greater than zero.";
        echo $updateMessage;
    } else {
        // Calculate total due amount (assuming each seat costs 150)
        $seatPrice = 150;
        $totalDue = count($selectedSeats) * $seatPrice;

        // Calculate change
        $change = $payment - $totalDue;

        if ($change < 0) {
            $updateMessage = "Insufficient payment. Total due is $totalDue.";
            echo $updateMessage;
        } else {
            $selectedSeatsString = implode(", ", $selectedSeats);

            // Check if a transaction already exists with the same CIN_ID and selected seats
            $check_sql = "SELECT COUNT(*) AS count 
                        FROM TRANSACTIONS T
                        JOIN CUSTOMER C ON T.TRANS_NUMBER = C.TRANS_NUMBER
                        JOIN CINEMA CI
                        WHERE CI.CIN_ID = '$cin_id' 
                        AND T.TRANS_SEATS = '$selectedSeatsString'";  // Ensure exact match of selected seats
            $check_result = $mysqli->query($check_sql);
            $check_row = $check_result->fetch_assoc();

            if ($check_row['count'] > 0) {
                $updateMessage = "Duplicate transaction detected. The selected seats and CIN_ID combination already exists.";
                echo $updateMessage;
            } else {
                // Start a transaction to ensure both inserts happen atomically
                $mysqli->begin_transaction();

                try {
                    // Insert the transaction into the TRANSACTIONS table
                    $insert_sql = "INSERT INTO TRANSACTIONS (TRANS_DUE, TRANS_PAYMENT, TRANS_CHANGE, TRANS_SEATS) 
                                   VALUES ('$totalDue', '$payment', '$change', '" . implode(", ", $selectedSeats) . "')";
                    $mysqli->query($insert_sql);

                    // Get the last inserted transaction ID (TRANS_NUMBER)
                    $transNumber = $mysqli->insert_id;

                    // Insert the corresponding customer into the CUSTOMER table
                    $insert_customer_sql = "INSERT INTO CUSTOMER (TRANS_NUMBER) 
                                            VALUES ('$transNumber')";
                    $mysqli->query($insert_customer_sql);

                    // Commit the transaction
                    $mysqli->commit();
                    $updateMessage = "Transaction and customer added successfully!";

                } catch (Exception $e) {
                    // Rollback in case of an error
                    $mysqli->rollback();
                    $updateMessage = "Error adding transaction: " . $e->getMessage();
                }
            }
        }
    }
}
if (isset($_POST['delete'])) {
    $trans_number = $_POST['trans_number']; // Get the transaction number from the form

    // Validate TRANS_NUMBER
    if (empty($trans_number) || !is_numeric($trans_number)) {
        echo "<div style='color: red;'>Error: Invalid transaction number.</div>";
        exit();
    }

    // Check if the transaction exists
    $checkSql = "SELECT * FROM TRANSACTIONS WHERE TRANS_NUMBER = '$trans_number'";
    $checkResult = $mysqli->query($checkSql);
    if ($checkResult->num_rows === 0) {
        echo "<div style='color: red;'>Error: The transaction you are trying to delete does not exist.</div>";
        exit();
    }

    // Delete the transaction and related customer
    $deleteSql = "DELETE FROM CUSTOMER WHERE TRANS_NUMBER = '$trans_number'";
    if ($mysqli->query($deleteSql)) {
        $deleteTransactionSql = "DELETE FROM TRANSACTIONS WHERE TRANS_NUMBER = '$trans_number'";
        if ($mysqli->query($deleteTransactionSql)) {
            echo "<div style='color: green;'>Transaction deleted successfully!</div>";
            header("Location: trans_database.php"); // Refresh the page after deletion
            exit();
        } else {
            echo "<div style='color: red;'>Error deleting transaction: " . $mysqli->error . "</div>";
        }
    } else {
        echo "<div style='color: red;'>Error deleting customer: " . $mysqli->error . "</div>";
    }
}



// Fetch transaction data to display
$sql = "SELECT T.TRANS_NUMBER, T.TRANS_DUE, T.TRANS_PAYMENT, T.TRANS_CHANGE, T.TRANS_SEATS, 
               C.CUS_NUMBER, CI.CIN_ID
        FROM TRANSACTIONS T
        JOIN CUSTOMER C ON T.TRANS_NUMBER = C.TRANS_NUMBER
        JOIN CINEMA CI ON CI.CIN_ID = '$cin_id'";
$result = $mysqli->query($sql);
?>  

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transactions</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@100..900&family=Varela+Round&display=swap" rel="stylesheet">
    <style>
        :root{
    --light-blue: #CEDEF0;
    --purple: #9D9AD9;
    --dark-blue: #6B9BD1;
    --main-font: 'Montserrat', sans-serif;
    --sub-font: 'Varela Round', sans-serif;
    }

    body{
        margin: 0;
        height: 100vh;
        width: 100vw;
        display: grid;
        grid-template-columns: 20% 80%;
        overflow: hidden
    }

    /*nav-bar style*/
    .nav-bar{
        height: 100vh;
        box-shadow: 0 4px 10px 4px rgb(0 0 0 / 0.5);
    }

    .profile{
        margin: 20px;
        display: flex;
        align-items: center;
        gap: 10px;
        flex-wrap: wrap;
        justify-content: flex-start;
    }

    .avatar{
        height: 50px;
        width: 50px;
        border-radius: 50px;
        background-color: var(--light-blue);
    }

    .emp-name{
        font-family: var(--main-font);
        font-size: 18px;
        margin-bottom: 0;
    }

    .emp-id, .label{
        font-family: var(--main-font);
        font-size: 14px;
        margin-top: 5px;
        color: gray;
    }

    .label{
        margin: 20px;
        margin-top: 20px;
    }

    .tab{
        display: flex;
        align-items: center;
        gap: 10px;
        flex-wrap: wrap;
        justify-content: flex-start;
        margin: 20px;
    }

    .bi {
        font-size: 30px; 
        line-height: 30px; 
        width: 30px; 
        height: 30px; 
        display: inline-block; 
        text-align: center;
    }

    a{
        color: black;
        text-decoration: none;
    }

    .tab-label{
        font-family: var(--main-font);
        font-size: 18px;
    }

    a:hover{
        color: var(--dark-blue);
    }

    .tab:hover{
        background-color: var(--light-blue);
        border-left: 5px solid var(--dark-blue);
        color: var(--dark-blue);
        margin-left: 0;
        margin-right: 0;
        padding: 0 20px 0 20px;
    }

    .tab:focus{
        background-color: var(--light-blue);
        border-left: 5px solid var(--dark-blue);
        color: var(--dark-blue);
        margin-left: 0;
        margin-right: 0;
        padding: 0 20px 0 20px;
    }

    /*database table style*/
    .trans-database{
        margin: 40px;
        margin-left: auto;
        margin-right: auto;
    }

    h1{
        margin: 0;
        font-family: var(--main-font);
        font-size: 30px;
    }

    form{
        margin: 20px 0 10px 0;
        font-family: var(--sub-font);
        font-size: 14px;
        display: flex;
        gap: 10px;
        align-items: center;
        width: 70vw;
    }

    .input{
        display: flex;
        flex-direction: column;
    }

    input[type=text], input[type=number]{
        width: 250px;
        height: 25px;
        border-radius: 5px;
        border: none;
        box-shadow: 4px 4px 4px 0 rgb(0 0 0 / 0.2);
    }

    input[type=text]:hover, select:hover{
        border: 2px solid black;
    }

    select{
        width: 150px;
        height: 25px;
        border-radius: 5px;
        border: none;
        box-shadow: 4px 4px 4px 0 rgb(0 0 0 / 0.2);
    }

    input[type="submit"]{
        width: 100px;
        height: 25px;
        border-radius: 5px;
        border: none;
        box-shadow: 4px 4px 4px 0 rgb(0 0 0 / 0.2);
        background-color: var(--purple);
        color: white;
        font-family: var(--sub-font);
        font-size: 14px;
        margin-top: auto;
    }

    table{
        width: 72vw;
        overflow-y: auto;
        margin-right: auto;
        border: 2px solid var(--dark-blue);
        border-collapse: collapse;
        box-shadow: 4px 4px 4px 0 rgb(0 0 0 / 0.2);
    }

    th{
        font-family: var(--main-font);
        font-size: 14px;
        text-align: left;
        padding: 10px;
        border: 2px solid var(--dark-blue);
        background-color: var(--dark-blue);
        color: white
    }

    td{
        font-family: var(--sub-font);
        font-size: 14px;
        text-align: left;
        padding: 10px;
        border: 2px solid var(--dark-blue);
        height: 15px;
    }

    .action:hover{
        color: var(--purple);
    }

    .table-container {
        height: 75vh; 
        overflow-y: auto; 
        overflow-x: hidden;
    }

    .table-container::-webkit-scrollbar {
        width: 8px;
    }

    .table-container::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 8px;
    }

    .table-container::-webkit-scrollbar-thumb {
        background: #888; 
        border-radius: 8px; 
    }

    .table-container::-webkit-scrollbar-thumb:hover {
        background: #555;
    }

    </style>
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
            <a href="../../main/main.php ">
                <div class="tab">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-tv" viewBox="0 0 16 16">
                        <path d="M2.5 13.5A.5.5 0 0 1 3 13h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5M13.991 3l.024.001a1.5 1.5 0 0 1 .538.143.76.76 0 0 1 .302.254c.067.1.145.277.145.602v5.991l-.001.024a1.5 1.5 0 0 1-.143.538.76.76 0 0 1-.254.302c-.1.067-.277.145-.602.145H2.009l-.024-.001a1.5 1.5 0 0 1-.538-.143.76.76 0 0 1-.302-.254C1.078 10.502 1 10.325 1 10V4.009l.001-.024a1.5 1.5 0 0 1 .143-.538.76.76 0 0 1 .254-.302C1.498 3.078 1.675 3 2 3zM14 2H2C0 2 0 4 0 4v6c0 2 2 2 2 2h12c2 0 2-2 2-2V4c0-2-2-2-2-2"/>
                      </svg>
                    <h3 class="tab-label">Movies</h3>
                </div>
            </a>

            <a href="trans_database.php">
                <div class="tab">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-tv" viewBox="0 0 16 16">
                        <path d="M2.5 13.5A.5.5 0 0 1 3 13h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5M13.991 3l.024.001a1.5 1.5 0 0 1 .538.143.76.76 0 0 1 .302.254c.067.1.145.277.145.602v5.991l-.001.024a1.5 1.5 0 0 1-.143.538.76.76 0 0 1-.254.302c-.1.067-.277.145-.602.145H2.009l-.024-.001a1.5 1.5 0 0 1-.538-.143.76.76 0 0 1-.302-.254C1.078 10.502 1 10.325 1 10V4.009l.001-.024a1.5 1.5 0 0 1 .143-.538.76.76 0 0 1 .254-.302C1.498 3.078 1.675 3 2 3zM14 2H2C0 2 0 4 0 4v6c0 2 2 2 2 2h12c2 0 2-2 2-2V4c0-2-2-2-2-2"/>
                      </svg>
                    <h3 class="tab-label">Transactions</h3>
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

        <div class="trans-database">
        <h1>Transactions Database</h1>

        <?php if ($updateMessage): ?>
            <div class="message"> <?php echo htmlspecialchars($updateMessage); ?> </div>
        <?php endif; ?>
        <a href="../../transaction/tickets/tickets.php?seats=<?php echo urlencode(implode(", ", $selectedSeats)); ?>&payment=<?php echo urlencode($payment); ?>&change=<?php echo urlencode($change); ?>&total_due=<?php echo urlencode($totalDue); ?>&trans_number=<?php echo urlencode($transNumber); ?>">Go To Current Ticket</a>

        <table>
            <tr>
                <th>Transaction Number</th>
                <th>Customer Number</th>
                <th>Transaction Due</th>
                <th>Transaction Payment</th>
                <th>Transaction Change</th>
                <th>Transaction Seats</th>
                <th>Cinema ID</th>
                <th>ACTION</th>

            </tr>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                <td><?php echo htmlspecialchars($row['TRANS_NUMBER']); ?></td>
                <td><?php echo htmlspecialchars($row['CUS_NUMBER']); ?></td>  <!-- Display Customer Number -->
                <td><?php echo htmlspecialchars($row['TRANS_DUE']); ?></td>
                <td><?php echo htmlspecialchars($row['TRANS_PAYMENT']); ?></td>
                <td><?php echo htmlspecialchars($row['TRANS_CHANGE']); ?></td>
                <td><?php echo htmlspecialchars($row['TRANS_SEATS']); ?></td>
                <td><?php echo htmlspecialchars($row['CIN_ID']); ?></td>
                <td><a class="action" href="delete.php?id=<?php echo $row['TRANS_NUMBER']; ?>">Delete</a></td>

                
                </tr>
            <?php endwhile; ?>
        </table>
    </div>
</body>
</html>

