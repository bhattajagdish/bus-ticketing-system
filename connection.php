<?php

$dbhost = "localhost";
$dbuser = "root";
$dbpass = "";
$dbname = "bus-reservation-system";

if(!$conn = mysqli_connect($dbhost,$dbuser,$dbpass,$dbname))
{

	die("failed to connect!");
}

// Set timezone for correct time calculations
date_default_timezone_set('Asia/Kathmandu'); // Nepal timezone (UTC+5:45)
mysqli_query($conn, "SET time_zone = '+05:45'");

?>