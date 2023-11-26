<?php
$servername = "barber-db-1";
$username = "root";
$password = "admin";
$i=0;
$dbname = "barber"; // dodać tutaj $i
$conn = mysqli_connect($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
$q = array();
$q[] = "create table admin (id int primary key not null  AUTO_INCREMENT, password BLOB, login varchar(255));"; // dodać tutaj $i
$password = bin2hex(random_bytes(10 / 2));
$login = bin2hex(random_bytes(10 / 2));
$q[] = "insert into admin values(null , SHA2(CONCAT('klucz', '$password'), 256), '$login');";
for($i=0;$i<count($q);$i++){
    mysqli_query($conn, $q[$i]);
}
echo "login: ".$login."<br>";
echo "haslo: ".$password."<br>";
mysqli_close($conn);