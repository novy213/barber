<?php
session_start();
if(!isset($_SESSION['loged'])){
    header('Location: '.'../admin/sites/login.php');
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
            echo "<script>location.href = '../admin/sites/login.php';</script>";
        }
        ?>
    </form>
</header>
<div class="con" style="text-align: center; width: 30%">
    <h2>Dodaj pracownika</h2>
    <form method="post" action="./php/Upload.php" enctype="multipart/form-data" style="border: 1px solid black;text-align: center">
        <label for="start">godzina rozpoczecia pracy: </label>
        <input type="time" name="hour_start" id="start" placeholder="Godzina rozpoczęcia pracy np. 9:00"><br><br>
        <label for="end">godzina zakonczenia pracy: </label>
        <input type="time" name="hour_end" id="end" placeholder="Godzina zakończenia pracy np. 18:00"><br><br>
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
        <label for="file">awatar pracownika: </label>
        <input type="file" name="file" id="file"> <br><br>
        <input type="submit" name="add" value="Dodaj pracownika">
    </form>
    <h2>Usun pracownika</h2>
    <form method="post" style="border: 1px solid black;text-align: center">
        <p>Wybierz pracownika</p>
        <?php
        include 'php/db.php';
        $q = "select * from barber;";
        $wynik = mysqli_query($conn, $q);
        $licznik = 1;
        while($row = mysqli_fetch_row($wynik)){
            echo "<label for='$row[0]'>$licznik. $row[1] $row[2]</label>";
            echo "<input type='checkbox' name='barberzy[]' value='$row[0]' id='$row[0]'><br>";
            $licznik++;
        }
        ?>
        <br><br>
        <input type="submit" name="delete_barber" value="usuń">
        <?php
        if(isset($_POST['delete_barber'])){
            $barberzy = isset($_POST['barberzy']) ? $_POST['barberzy'] : array();
            for($i=0;$i<count($barberzy);$i++){
                $q = "select img_url from barber where id = $barberzy[$i];";
                $wynik = mysqli_fetch_row(mysqli_query($conn, $q));
                if($wynik[0]!=null || $wynik[0]!="") {
                    $file_name = basename($wynik[0]);
                    $filePath = "barber_img/".$file_name;
                    if (file_exists($filePath)) {
                        unlink($filePath);
                    }
                }
                $q = "delete from barber where id = $barberzy[$i];";
                mysqli_query($conn, $q);
            }
            echo "<script>location.href = 'index.php';</script>";
        }
        ?>
    </form>
    <h2>Usun uzytkownika</h2>
    <form method="post" style="border: 1px solid black;text-align: center">
        <p>Wybierz uzytkownika</p>
        <?php
        include 'php/db.php';
        $q = "select * from user;";
        $wynik = mysqli_query($conn, $q);
        $licznik = 1;
        while($row = mysqli_fetch_row($wynik)){
            echo "<label for='$row[0]'>$licznik. $row[2] $row[3]</label>";
            echo "<input type='checkbox' name='users[]' value='$row[0]' id='$row[0]'><br>";
            $licznik++;
        }
        ?>
        <br><br>
        <input type="submit" name="delete_user" value="usuń">
        <?php
        if(isset($_POST['delete_user'])){
            $users = isset($_POST['users']) ? $_POST['users'] : array();
            for($i=0;$i<count($users);$i++){
                $q = "delete from user where id = $users[$i];";
                mysqli_query($conn, $q);
            }
            echo "<script>location.href = 'index.php';</script>";
        }
        ?>
    </form>
    <h2>Zbanuj uzytkownika</h2>
    <form method="post" style="border: 1px solid black;text-align: center">
        <p>Wybierz uzytkownika</p>
        <?php
        include 'php/db.php';
        $q = "select * from user where ban = 0;";
        $wynik = mysqli_query($conn, $q);
        $licznik = 1;
        while($row = mysqli_fetch_row($wynik)){
            echo "<label for='$row[0]'>$licznik. $row[2] $row[3]</label>";
            echo "<input type='checkbox' name='users[]' value='$row[0]' id='$row[0]'><br>";
            $licznik++;
        }
        ?>
        <br><br>
        <input type="submit" name="ban_user" value="zbanuj">
        <?php
        if(isset($_POST['ban_user'])){
            $users = isset($_POST['users']) ? $_POST['users'] : array();
            for($i=0;$i<count($users);$i++){
                $q = "update user set ban = 1 where id = $users[$i];";
                mysqli_query($conn, $q);
            }
            echo "<script>location.href = 'index.php';</script>";
        }
        ?>
    </form>
    <h2>Odbanuj uzytkownika</h2>
    <form method="post" style="border: 1px solid black;text-align: center">
        <p>Wybierz uzytkownika</p>
        <?php
        include 'php/db.php';
        $q = "select * from user where ban = 1;";
        $wynik = mysqli_query($conn, $q);
        $licznik = 1;
        while($row = mysqli_fetch_row($wynik)){
            echo "<label for='$row[0]'>$licznik. $row[2] $row[3]</label>";
            echo "<input type='checkbox' name='users[]' value='$row[0]' id='$row[0]'><br>";
            $licznik++;
        }
        ?>
        <br><br>
        <input type="submit" name="unban_user" value="odbanuj">
        <?php
        if(isset($_POST['unban_user'])){
            $users = isset($_POST['users']) ? $_POST['users'] : array();
            for($i=0;$i<count($users);$i++){
                $q = "update user set ban = 0 where id = $users[$i];";
                mysqli_query($conn, $q);
            }
            echo "<script>location.href = 'index.php';</script>";
        }
        ?>
    </form>
</div>
</body>
</html>