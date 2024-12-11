<?php
$user='root';
$password='balagtas2csa';
$servername='localhost:3310';
$database='movie_reservation_system';

//create an instance to see if the database connects to the server
$mysqli=new mysqli($servername,$user,$password,$database);

if($mysqli->connect_error)
{
    die('Connect Error('.$mysqli->maxdcb_connect_errno.')').maxdb_connect_error;
}

$errorMessage = null;

if(isset($_POST['submit'])){

    $USERNAME = $_POST['user-input'];
    $PASSWORD = $_POST['pass-input'];

    $sql="SELECT EMP_ROLE from EMPLOYEE WHERE EMP_LNAME='$USERNAME' AND EMP_ID='$PASSWORD'";
    $result = $mysqli->query($sql);

    if (strlen($PASSWORD) !== 8 || !ctype_digit($PASSWORD) ) {
        $errorMessage = 'Username and/or password is incorrect. Please try again.';
    } elseif ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        if($row['EMP_ROLE'] === 'Admin')
            header('Location: ../admin/emp_database.html');
        else if($row['EMP_ROLE'] === 'Employee')
            header('Location: ./transaction/transaction.html');   
    } else {
        $errorMessage = 'Username and/or password is incorrect. Please try again.';
    }
}

if ($errorMessage) {
    echo "<div style='color: red;'>$errorMessage</div>";
}

$mysqli->close();
?>
