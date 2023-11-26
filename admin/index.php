<?php
session_start();
if(!isset($_SESSION['loged'])){
    header('Location: '.'/admin/sites/login.php');
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="styles/indexStyle.css">
    <title>admin</title>
</head>
<body>
<header>
    <h1>Panel administratora</h1>
</header>
<div class="con">
    <h3>Dodaj pracownika</h3>
    <form>
        <?php
        include 'php/db.php';
        //unset($_SESSION['loged']);
        ?>
    </form>
</div>
</body>
</html>