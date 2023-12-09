<?php

namespace app\controllers;

use app\components\Controller;
use sngrl\PhpFirebaseCloudMessaging\Client;
use sngrl\PhpFirebaseCloudMessaging\Message;
use sngrl\PhpFirebaseCloudMessaging\Recipient\Device;
use sngrl\PhpFirebaseCloudMessaging\Notification;

class PushController extends Controller
{
    public function actionPushnoti(){
        $server_key = 'AAAAgX5aCbI:APA91bFWRppLu34vfmvXfcSN4IZH3oiXJF92hneHyTf7TJiNbguEjYjAUVVFX0TmZh5IpA_cmA-5pA8lNe7CE-S2cwUAixO8kvJhhKIziKWa2VNqMScmHtgS8XM_JwJN31A8wo42BrLS';
        $client = new Client();
        $client->setApiKey($server_key);

        $message = new Message();
        $message->setPriority('high');
        $message->addRecipient(new Device('xOZCohBL_nVysa6XnoBg28'));
        $message
            ->setNotification(new Notification('Ala ma kota tytul', 'w ogolnym rozrachunku kot ma kurwa ale'))
            ->setData(['key' => 'KOT'])
        ;

        $response = $client->send($message);
        var_dump($response->getStatusCode());
        var_dump($response->getBody()->getContents());
    }
}