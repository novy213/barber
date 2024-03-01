<?php

namespace app\controllers;

use Bluerhinos\phpMQTT;
use yii\console\Controller;
use yii\db\Connection;
use Yii;


class MqttController extends Controller
{
    public function actionIndex()
    {
        // Utwórz połączenie z bazą danych MySQL
        $db = Yii::$app->db;

        // Utwórz klienta MQTT
        $client = new phpMQTT('jakubsolarek.pl', 1883, 'client_123');
        $client->connect();
        $client->subscribeAndWaitForMessage('testTopic', 2);
        //napisz mi listener na otrzymanie wiadomości
        $client->message();

       /* $client->onMessage(function($message) use ($db) {
            $topic = $message->topic;
            $content = $message->payload;

            echo "Received message: $content on topic: $topic\n";

            // Zapisz wiadomość do bazy danych MySQL
            $db->createCommand()
                ->insert('messages', [
                    'topic' => $topic,
                    'message' => $content
                ])->execute();

            echo "Message inserted into database\n";
        });*/

        //zrób tak aby ten kod się ciągle nasłuchiwał
        $client->proc(true);
        return "alamakota";
    }

    private function saveMessageToDatabase($topic, $message)
    {
        // Przetwórz otrzymaną wiadomość (np. zapisz do bazy danych)
        $receivedTime = date('Y-m-d H:i:s');

        $messageModel = new Message();
        $messageModel->topic = $topic;
        $messageModel->message = $message;
        $messageModel->date = $receivedTime;

        if ($messageModel->validate()) {
            $messageModel->save();
        } else {
            Yii::error('Błąd podczas zapisywania wiadomości do bazy danych: ' . print_r($messageModel->errors, true));
        }
    }
}
