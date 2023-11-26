<?php
session_start();
if(isset($_SESSION['loged'])){
    header('Location: '.'../index.php');
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>
    <form method="post">
        <input type="text" name="login">
        <input type="password" name="pass">
        <input type="submit" name="submit">
        <?php
        include '../php/db.php';
        if(isset($_POST['submit'])){
            $pass = $_POST['pass'];
            $login = $_POST['login'];
            $q = "select * from admin where login='$login' && password = SHA2(CONCAT('klucz', '$pass'), 256);";
            $wynik = mysqli_query($conn, $q);
            if(!is_null($wynik)){
                $_SESSION['loged'] = 1;
                echo "<script>location.href = '../index.php';</script>";
            }
        }
        ?>
    </form>
</body>
</html>