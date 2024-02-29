<?php

namespace app\commands;

use app\models\Message;
use Yii;
use yii\console\Controller;
use yii\console\ExitCode;
use yii\helpers\Console;
use Mosquitto\Client as MosquittoClient;

class MqttController extends \app\components\Controller
{
    public function actionSubscribe()
    {
        $post = $this->getJsonInput();
        $mqtt = new MosquittoClient();

        // Konfiguracja połączenia z brokerem MQTT
        $mqtt->connect('localhost', 1883); // Zmień na rzeczywiste dane brokera MQTT

        // Ustawienie callbacka dla otrzymanych wiadomości
        $mqtt->onMessage(function($message) use ($post) {
            $this->saveMessageToDatabase($message, $post);
        });

        // Subskrybowanie wszystkich wiadomości
        $mqtt->subscribe('#', 0);

        // Uruchomienie pętli głównej
        $mqtt->loopForever();
    }

    private function saveMessageToDatabase($message, $post)
    {
        // Odczytanie czasu otrzymania wiadomości
        $receivedTime = date('Y-m-d H:i:s');

        $message = new Message();
        $message->message = $post->message;
        $message->barber_id = $post->barber_id;
        $message->from= $post->from;
        $message->user_id = $post->user_id;
        $message->date = $receivedTime;
        if($message->validate()) {
            $message->save();
            return[
                'error' => false,
                'message' => null
            ];
        } else {
            return[
                'error' => true,
                'message' => $message->getErrorSummary(false)
            ];
        }
    }
}