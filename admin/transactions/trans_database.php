<?php
session_start();
include('../../database_connection.php');

$errorMessage = null;

if (!isset($_SESSION['EMP_FNAME']) && !isset($_SESSION['EMP_ID'])) {
    header('Location: ../login.php');
    exit();
}

$updateMessage = null;

// Add a check for the user role (admin or employee)
$isAdmin = isset($_SESSION['ROLE']) && $_SESSION['ROLE'] == 'Admin'; // Check if user is admin

function validateInput($trans_number, $trans_due, $trans_payment, $mysqli) {
    global $errorMessage;

    // Check for blank fields
    if (empty($trans_number) || empty($trans_due) || empty($trans_payment)) {
        $errorMessage = "All fields are required.";
        return false;
    }

    // Check for correct data type
    if (!is_numeric($trans_due) || !is_numeric($trans_payment)) {
        $errorMessage = "Transaction Due and Payment must be numeric.";
        return false;
    }

    // Check for referential integrity
    $check_foreign_key_sql = "SELECT * FROM TRANSACTIONS WHERE TRANS_NUMBER = '$trans_number'";
    $result = $mysqli->query($check_foreign_key_sql);
    if ($result->num_rows === 0) {
        $errorMessage = "Transaction Number does not exist.";
        return false;
    }

    // Check for duplicate records (if applicable)
    $check_duplicate_sql = "SELECT * FROM TRANSACTIONS WHERE TRANS_NUMBER = '$trans_number' AND TRANS_DUE = '$trans_due' AND TRANS_PAYMENT = '$trans_payment'";
    $duplicate_result = $mysqli->query($check_duplicate_sql);
    if ($duplicate_result->num_rows > 0) {
        $errorMessage = "Duplicate transaction record.";
        return false;
    }

    return true;
}

// Handle the Update functionality
if (isset($_POST['update'])) {
    $trans_number = $_POST['trans_number'];
    $trans_due = $_POST['trans_due'];
    $trans_payment = $_POST['trans_payment'];
    $trans_change = $trans_payment - $trans_due;

    if (validateInput($trans_number, $trans_due, $trans_payment, $mysqli)) {
        $update_sql = "UPDATE TRANSACTIONS 
                       SET TRANS_DUE = $trans_due, TRANS_PAYMENT = $trans_payment, TRANS_CHANGE = $trans_change 
                       WHERE TRANS_NUMBER = $trans_number";

        if ($mysqli->query($update_sql)) {
            $updateMessage = "Transaction updated successfully!";
        } else {
            $updateMessage = "Error updating transaction: " . $mysqli->error;
        }
    }
}

// Fetch data for display
$sql = "SELECT T.TRANS_NUMBER, T.TRANS_DUE, T.TRANS_PAYMENT, T.TRANS_CHANGE, T.TRANS_DATE, T.EMP_ID, 
               E.EMP_FNAME, E.EMP_LNAME
        FROM TRANSACTIONS T
        JOIN EMPLOYEE E ON T.EMP_ID = E.EMP_ID";
$result = $mysqli->query($sql);

// Redirect based on user role (admin or employee)
if ($isAdmin) {

} else {
    // Employee is redirected to ticket.html
    header('Location: ../../transaction/tickets/tickets.html');
    exit();
}

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

    .title{
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
            <h4 class="label">Databases</h4>
            <a href="">
                <div class="tab">
                    <i class="bi bi-people-fill"></i>
                    <h3 class="tab-label">Employees</h3>
                </div>
            </a>
            <a href="../transactions/trans_database.php">
                <div class="tab">
                    <i class="bi bi-bank2"></i>
                    <h3 class="tab-label">Transactions</div>
                </div>
            </a>
            <a href="../tickets/tickets.php">
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

        <div class="trans-database">
        <h1>Transactions Database</h1>

        <?php if ($updateMessage): ?>
            <div class="message"> <?php echo htmlspecialchars($updateMessage); ?> </div>
        <?php endif; ?>

        <div class="form-container">
            <form action="" method="POST">
                <label for="trans_number">Transaction Number:</label>
                <input type="text" name="trans_number" required><br>

                <label for="trans_due">Transaction Due:</label>
                <input type="number" name="trans_due" required><br>

                <label for="trans_payment">Transaction Payment:</label>
                <input type="number" name="trans_payment" required><br>

                <input type="submit" name="update" value="Update">
            </form>
        </div>

        <table>
            <tr>
                <th>Transaction Number</th>
                <th>Transaction Due</th>
                <th>Transaction Payment</th>
                <th>Transaction Change</th>
                <th>Transaction Date</th>
                <th>Employee</th>
            </tr>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['TRANS_NUMBER']); ?></td>
                    <td><?php echo htmlspecialchars($row['TRANS_DUE']); ?></td>
                    <td><?php echo htmlspecialchars($row['TRANS_PAYMENT']); ?></td>
                    <td><?php echo htmlspecialchars($row['TRANS_CHANGE']); ?></td>
                    <td><?php echo htmlspecialchars($row['TRANS_DATE']); ?></td>
                    <td><?php echo htmlspecialchars($row['EMP_FNAME'] . ' ' . $row['EMP_LNAME']); ?></td>
                </tr>
            <?php endwhile; ?>
        </table>
    </div>
</body>
</html>

