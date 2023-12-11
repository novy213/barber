<?php

namespace app\models;

use ExpoSDK\Expo;
use ExpoSDK\ExpoMessage;

class PushNotification
{
    public static function pushNoti($recipient, $expoMessage)
    {
        (new Expo)->send($expoMessage)->to($recipient)->push();
    }
}