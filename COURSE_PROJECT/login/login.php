<?php
session_start();
include ('../database_connection.php');

$errorMessage = null;

if (isset($_POST['submit'])) {

    $USERNAME = $_POST['user-input'];
    $PASSWORD = $_POST['pass-input'];

    $sql = "SELECT EMP_ID, EMP_FNAME, EMP_LNAME, EMP_ROLE FROM EMPLOYEE WHERE EMP_LNAME = '$USERNAME' AND EMP_ID = '$PASSWORD'";
    $result = $mysqli->query($sql);

    if (strlen($PASSWORD) !== 8 || !ctype_digit($PASSWORD)) {
        $errorMessage = 'Username and/or password is incorrect. Please try again.';
    } elseif ($result->num_rows > 0) {
        $row = $result->fetch_assoc();

        $_SESSION['EMP_FNAME'] = $row['EMP_FNAME'];
        $_SESSION['EMP_LNAME'] = $row['EMP_LNAME'];        

        if ($row['EMP_ROLE'] === 'Admin') {
            header('Location: ../admin/emp_database/emp_database.php');
            exit();
        } else if ($row['EMP_ROLE'] === 'Employee') {
            header('Location: ../employee/movies/movie_database.php');
            exit();
        }
    } else {
        $errorMessage = 'Username and/or password is incorrect. Please try again.';
    }
}

if ($errorMessage) {
    echo "<div style='color: red;'>$errorMessage</div>";
}

$mysqli->close();
?>
