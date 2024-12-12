<?php
session_start();
include('../../database_connection.php');

if (!isset($_SESSION['EMP_FNAME']) || !isset($_SESSION['EMP_ID'])) {
    header('Location: ../login.php');
    exit();
}

if (isset($_GET['id'])) {
    $emp_id = $_GET['id'];
    $sql = "DELETE FROM movie_reservation_system.EMPLOYEE WHERE EMP_ID = $emp_id";
    if ($mysqli->query($sql) === TRUE) {
        header("Location: emp_database.php?delete_success=1");
    } else {
        header("Location: emp_database.php?delete_error=1");
    }
} else {
    header("Location: emp_database.php?delete_error=1");
}

$mysqli->close();
?>
