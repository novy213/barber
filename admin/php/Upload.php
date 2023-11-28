<?php
include "db.php";
if(isset($_POST['add'])) {
    $filename = $_FILES['file']['name'];
    $location = "../barber_img/".$filename;
    //$serverPath = 'https://jakubsolarek.pl/test/barber/admin/barber_img/'.$filename;
    $serverPath = null;
    $id = $_POST['selected_user'];
    $hour_start = $_POST['hour_start'];
    $hour_end = $_POST['hour_end'];
    if($id == "" ||$hour_start==""||$hour_end==""){
        echo "prosze uzupelnic formularz poprawnie, nie dodano pracownika<br>";
        echo "
<script>
function cofnij(){
    location.href = '../index.php';
}
</script>
<button onclick='cofnij()'>Cofnij</button>";
        die;
    }
    $dateTime = DateTime::createFromFormat('H:i', $hour_start);
    $dateTime2 = DateTime::createFromFormat('H:i', $hour_end);
    $minuta = $dateTime->format('i');
    $minuta2 = $dateTime2->format('i');
    if (!in_array($minuta, ['00', '15', '30', '45']) || !in_array($minuta2, ['00', '15', '30', '45'])) {
        echo "minuta jest niepoprawna, dozwolona minuta: 00, 15, 30 lub 45, nie dodano pracownika<br>";
        echo "
<script>
function cofnij(){
    location.href = '../index.php';
}
</script>
<button onclick='cofnij()'>Cofnij</button>";
        die;
    }
    $q = "select * from barber where user_id = $id;";
    $wynik = mysqli_fetch_row(mysqli_query($conn, $q));
    $user = null;
    if($wynik){
        echo "taki pracownik juz istnieje";
        echo "
<script>
function cofnij(){
    location.href = '../index.php';
}
</script>
<button onclick='cofnij()'>Cofnij</button>";
        die;
    }
    else {
        $q = "select * from user where id = $id;";
        $user = mysqli_fetch_row(mysqli_query($conn, $q));
    }
    if($filename) {
        $serverPath = 'http://localhost/admin/barber_img/'.$filename;
        if (move_uploaded_file($_FILES['file']['tmp_name'], $location)) {
            echo "Zdjęcie zostało przesłane poprawnie<br>";
        } else {
            echo "Zdjęcie nie zostało przesłane poprawnie<br>";
            die;
        }
    }
    else $serverPath = null;
    $q = array();
    $q[] = "insert into barber values(null, '$user[2]', '$user[3]', $id, '$hour_start' , '$hour_end' ,'$serverPath');";
    $q[] = "update user set admin = 1 where id = $id;";
    $q[] = "update user set verified = 1 where id = $id;";
    $licznik = 0;
    for($i=0; $i<count($q);$i++){
        $wynik = mysqli_query($conn, $q[$i]);
        $licznik++;
    }
    if($licznik = 2){
        echo "pracownik został dodany poprawnie";
    }
    echo "
<script>
function cofnij(){
    location.href = '../index.php';
}
</script>
<button onclick='cofnij()'>Cofnij</button>";
}