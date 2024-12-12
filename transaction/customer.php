<?php 
include("../database_connection.php");

$sql = "SELECT IFNULL(MAX(CUS_NUMBER), 0) + 1 AS cus_id FROM CUSTOMER";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    echo json_encode(['cus_id' => $row['cus_id']]);
} else {
    echo json_encode(['cus_id' => 1]);
}

$mysqli->close();

?>