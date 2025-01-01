<?php
// config.php - Database configuration

$servername = "localhost";
$username = "root";
$password = "1234";  // Your root password
$dbname = "todo_app";  // The name of your database

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
