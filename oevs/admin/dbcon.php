<?php
$servername = "localhost"; // Confirmed correct for x10hosting
$username = "root";
$password = "";
$dbname = "oevs_db";

// Create connection
$conn = mysqli_connect($servername, $username, $password, $dbname);

// Check connection
if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}
?>
