<?php

namespace app\controllers;

use app\components\Controller;
use ExpoSDK\Expo;
use ExpoSDK\ExpoMessage;

class PushController extends Controller
{
    public function actionPushnoti(){
        /**
         * Composed messages, see above
         * Can be an array of arrays, ExpoMessage instances will be made internally
         */
        $messages = [
            [
                'title' => 'Test notification',
            ],
            new ExpoMessage([
                'title' => 'Notification for default recipients',
                'body' => 'Because "to" property is not defined',
            ]),
        ];

        /**
         * These recipients are used when ExpoMessage does not have "to" set
         */
        $defaultRecipients = [
            'ExponentPushToken[CPEX0yL2NZRO25c5lfBsh2]',
            'ExponentPushToken[xOZCohBL_nVysa6XnoBg28]'
        ];

        (new Expo)->send($messages)->to($defaultRecipients)->push();
    }
}