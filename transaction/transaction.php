<?php
// Include the database connection
include('../database_connection.php');

// Example: Set a default employee ID or retrieve it from the session
$empId = 1;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['transaction'])) {
    $transactionNumber = 123; // Transaction number should come from the logic of your app (perhaps auto-generated or set in a session)
    $payment = 500; // Example payment amount

    $insertTransactionQuery = "INSERT INTO TRANSACTIONS (TRANS_NUMBER, TRANS_DUE, TRANS_PAYMENT, TRANS_DATE, EMP_ID)
                            VALUES (?, ?, ?, NOW(), ?)";
    $stmt = $mysqli->prepare($insertTransactionQuery);
    $stmt->bind_param("iiiii", $transactionNumber, $payment, $payment, $empId);

    if ($stmt->execute()) {
        // Get the last inserted transaction number
        $transactionNumber = $stmt->insert_id;  
    } else {
        echo "<p>Failed to record the transaction.</p>";
        exit; // Stop further execution if transaction failed
    }

    // Redirect to the seats page with the transaction number
    header("Location: seats/seats.php?transaction_number=$transactionNumber");
    exit();
}
?>