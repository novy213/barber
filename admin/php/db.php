<?php
$servername = "barber-db-1";
$username = "root";
$password = "admin";
$dbname = "barber_admin";
$conn = mysqli_connect($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}