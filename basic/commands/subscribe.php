<?php

use PhpMqtt\Client\MqttClient;
use PhpMqtt\Client\Exceptions\MqttClientException;
use Yii;

require(__DIR__ . '/../vendor/autoload.php');
require(__DIR__ . '/../config/bootstrap.php');

$server = 'mqtt.example.com';
$port = 1883;
$clientId = 'yii2-client-subscribe';
$username = 'your-username';
$password = 'your-password';

try {
    $mqttClient = new MqttClient($server, $port, $clientId);
    $mqttClient->connect($username, $password);

    $topic = 'your/topic';
    $mqttClient->subscribe($topic, function ($topic, $message) {
        Yii::info("Received message: $message on topic: $topic");
    }, 0);

    $mqttClient->loop(true); // Start looping to process incoming messages
} catch (MqttClientException $e) {
    Yii::error("Unable to connect to MQTT server: " . $e->getMessage());
}
