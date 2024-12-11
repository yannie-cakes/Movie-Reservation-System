<?php
$servername = "localhost:3310";
$username = "root";
$password = "midnightsonatine"; // Please change the password as per your machine
$dbname = "movie_reservation_system";

// Create a connection to the MySQL database
$mysqli = new mysqli($servername, $username, $password, $dbname);

// Check if the connection was successful
if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error); // Terminate script and show error message if connection fails
}

// Optionally set character set to ensure proper encoding
$mysqli->set_charset("utf8");

// Now you can use the $mysqli object for database operations
?>