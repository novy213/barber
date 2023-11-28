<?php
include 'php/db.php';
$q = array();
$q[] = "create table admin (id int primary key not null  AUTO_INCREMENT, password BLOB, login varchar(255));";
$password = bin2hex(random_bytes(10 / 2));
$login = bin2hex(random_bytes(10 / 2));
$q[] = "insert into admin values(null , SHA2(CONCAT('klucz', '$password'), 256), '$login');";
for($i=0;$i<count($q);$i++){
    mysqli_query($conn, $q[$i]);
}
echo "login: ".$login."<br>";
echo "haslo: ".$password."<br>";
