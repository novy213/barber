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
        $fcmUrl = 'https://fcm.googleapis.com/fcm/send';
        $args = func_get_args();
        $response = ["status" => 0, "message" => "Notification couldn't be sent"];

        $title = "Hey test notification";
        $body = "";
        $apiKey = "AAAAgX5aCbI:APA91bFWRppLu34vfmvXfcSN4IZH3oiXJF92hneHyTf7TJiNbguEjYjAUVVFX0TmZh5IpA_cmA-5pA8lNe7CE-S2cwUAixO8kvJhhKIziKWa2VNqMScmHtgS8XM_JwJN31A8wo42BrLS";//Server key under the Project settings
        $tokenArr = ["ExponentPushToken[xOZCohBL_nVysa6XnoBg28]"];
        $refId = 123;
        $msgNotification = [
            "title" => $title,
            "body" => $body
        ];
        $extraNotificationData = [
            "refId" => $refId,
            "title" => $title
        ];

        $fcmNotification = [
            "registration_ids" => $tokenArr,
            "notification" => $msgNotification,
            "data" => $extraNotificationData
        ];
        $headers = [
            "Authorization: key=" . $apiKey,
            "Content-Type: application/json"
        ];

        $encodedData = json_encode($fcmNotification);
        // echo "<pre>";print_r($fcmNotification);exit;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,$fcmUrl);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $encodedData);
        $result = curl_exec($ch);
        if ($result === FALSE) {
            die("Curl failed: " . curl_error($ch));
        }
        print_r($result);exit;
        curl_close($ch);
        $response = ["status" => 1, "message" => "Notification sent to users", "payload" => $result];

        return $response;
    }
}