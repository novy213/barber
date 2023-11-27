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
    <form method="post">
        <input type="submit" name="wyloguj" value="wyloguj">
        <?php
        if(isset($_POST['wyloguj'])){
            unset($_SESSION['loged']);
            echo "<script>location.href = '/admin/sites/login.php';</script>";
        }
        ?>
    </form>
</header>
<div class="con">
    <h3>Dodaj pracownika</h3>
    <form method="post" action="/admin/php/Upload.php" enctype="multipart/form-data">
        <input type="text" name="name" placeholder="Imie barbera"><br><br>
        <input type="text" name="last_name" placeholder="Nazwisko barbera"><br><br>
        <input type="text" name="hour_start" placeholder="Godzina rozpoczęcia np. 9:00"><br><br>
        <input type="text" name="hour_end" placeholder="Godzina zakończenia np. 18:00"><br><br>
        <label for="users">Wybierz użytkownika</label>
        <select name="selected_user" id="users">
        <?php
        include 'php/db.php';
            $q = "select * from user;";
            $wynik = mysqli_query($conn, $q);
            while($row = mysqli_fetch_row($wynik)){
                echo "<option value='$row[0]'>$row[2] $row[3]</option>";
            }
        ?>
        </select>
        <br><br>
        <input type="file" name="file"> <br><br>
        <input type="submit" name="add" value="Dodaj barbera">
    </form>
</div>
</body>
</html>