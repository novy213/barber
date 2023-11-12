<?php

$url = 'http://localhost/basic/web/send';

$ch = curl_init($url);

curl_setopt($ch, CURLOPT_POST, 1);

curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$response = curl_exec($ch);

if (curl_errno($ch)) {
    echo 'Błąd cURL: ' . curl_error($ch);
}

curl_close($ch);

echo $response;