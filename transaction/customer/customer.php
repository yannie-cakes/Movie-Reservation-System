<?php 
include("../database_connection.php");

$trans_number = $_POST['trans_number'];

$sql = "INSERT INTO CUSTOMER (TRANS_NUMBER) VALUES (?)";

$stmt = $mysqli->prepare($sql);

$stmt->bind_param("i", $trans_number);

if ($stmt->execute()) {
    $cus_number = $stmt->insert_id;

    echo json_encode(['cus_id' => $cus_number]);
} else {
    echo json_encode(['error' => 'Failed to insert customer']);
}

$stmt->close();
$mysqli->close();

?>