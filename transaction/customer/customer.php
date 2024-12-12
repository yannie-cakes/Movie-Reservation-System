<?php 
include("../database_connection.php");

$sql = "SELECT MAX(CUS_NUMBER) AS cus_id FROM CUSTOMER";
$result = $conn->query($sql);

$response = array();
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $response['cus_id'] = $row['cus_id'] + 1; // Incrementing for next ID
} else {
    $response['cus_id'] = 1; // Default first ID
}

echo json_encode($response);

$mysqli->close();

?>