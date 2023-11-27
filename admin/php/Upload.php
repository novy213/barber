<?php
include "db.php";
if(isset($_POST['add'])) {
    $filename = $_FILES['file']['name'];
    $location = "../barber_img/".$filename;
    //$serverPath = 'https://jakubsolarek.pl/test/barber/admin/barber_img/'.$filename;
    $serverPath = 'http://localhost/admin/barber_img/'.$filename;
    if($filename) {
        if (move_uploaded_file($_FILES['file']['tmp_name'], $location)) {
            echo "Zdjęcie zostało przesłane poprawnie<br>";
        } else {
            echo "Zdjęcie nie zostało przesłane poprawnie<br>";
            die;
        }
    }

    $id = $_POST['selected_user'];
    $name = $_POST['name'];
    $last_name = $_POST['last_name'];
    $hour_start = $_POST['hour_start'];
    $hour_end = $_POST['hour_end'];
    $q = array();
    $q[] = "insert into barber values(null, '$name', '$last_name', $id, '$hour_start' , '$hour_end' ,'$serverPath');";
    $q[] = "update user set admin = 1 where id = $id;";
    $q[] = "update user set verified = 1 where id = $id;";
    $licznik = 0;
    for($i=0; $i<count($q);$i++){
        $wynik = mysqli_query($conn, $q[$i]);
        $licznik++;
    }
    if($licznik = 2){
        echo "Barber został dodany poprawnie";
    }
    echo "
<script>
function cofnij(){
    location.href = '../index.php';
}
</script>
<button onclick='cofnij()'>Cofnij</button>";
}