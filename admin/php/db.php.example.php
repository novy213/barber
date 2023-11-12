<?php
$servername = "your-server-name";
$username = "your-user-name";
$password = "your-password";
$dbname = "your-db";
$conn = mysqli_connect($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}