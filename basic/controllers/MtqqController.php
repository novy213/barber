<?php
namespace app\controllers;

use yii\web\Controller;
use PhpMqtt\Client\MqttClient;
use PhpMqtt\Client\Exceptions\MqttClientException;

class MqttController extends Controller
{
    private $mqttClient;

    public function init()
    {
        parent::init();

        $server = 'mqtt.example.com'; // Adres serwera MQTT
        $port = 1883;                 // Port serwera MQTT
        $clientId = 'yii2-client';    // ID klienta MQTT
        $username = 'your-username';  // Użytkownik (opcjonalnie)
        $password = 'your-password';  // Hasło (opcjonalnie)

        try {
            $this->mqttClient = new MqttClient($server, $port, $clientId);
            $this->mqttClient->connect($username, $password);
        } catch (MqttClientException $e) {
            // Obsługa błędów
            Yii::error("Unable to connect to MQTT server: " . $e->getMessage());
        }
    }

    public function actionPublish()
    {
        try {
            $topic = 'your/topic';
            $message = 'Hello, MQTT!';
            $this->mqttClient->publish($topic, $message, 0);
            return 'Message published';
        } catch (MqttClientException $e) {
            return 'Failed to publish message: ' . $e->getMessage();
        }
    }

    public function actionSubscribe()
    {
        try {
            $topic = 'your/topic';
            $this->mqttClient->subscribe($topic, function ($topic, $message) {
                Yii::info("Received message: $message on topic: $topic");
            }, 0);

            $this->mqttClient->loop(true); // Start looping to process incoming messages
        } catch (MqttClientException $e) {
            return 'Failed to subscribe to topic: ' . $e->getMessage();
        }
    }

    public function __destruct()
    {
        if ($this->mqttClient) {
            $this->mqttClient->disconnect();
        }
    }
}
