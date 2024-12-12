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
?>