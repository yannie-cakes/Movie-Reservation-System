<?php
session_start();
include('../../database_connection.php');


if (isset($_GET['id'])) {
    $TRANS_NUMBER = $_GET['id'];

    $deleteCustomerSql = "DELETE FROM customer WHERE TRANS_NUMBER = '$TRANS_NUMBER'";
    if (!$mysqli->query($deleteCustomerSql)) {
        echo "<div style='color: red;'>Error: " . $mysqli->error . "</div>";
        exit();
    }

    $sql = "DELETE FROM movie_reservation_system.TRANSACTIONS WHERE TRANS_NUMBER = $TRANS_NUMBER";
    if ($mysqli->query($sql) === TRUE) {
        header("Location: trans_database.php?delete_success=1");
    } else {
        header("Location: trans_database.php?delete_error=1");
    }
} else {
    header("Location: trans_database.php?delete_error=1");
}

$mysqli->close();
?>
