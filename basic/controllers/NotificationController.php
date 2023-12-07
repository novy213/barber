<?php

namespace app\controllers;

use app\models\Price;
use app\models\SendSMS;
use app\models\Visit;
use DateTime;
use yii\db\Expression;
use yii\i18n\Formatter;
use yii\web\Controller;

class NotificationController extends \app\components\Controller
{
    public function actionSendnoti(){
        date_default_timezone_set('Europe/Warsaw');
        $visits = Visit::find()->where(['>', 'date', new Expression('DATE_SUB(NOW(), INTERVAL 1 HOUR)')])->andWhere(['group'=>null])->all();
        for($i=0;$i<count($visits);$i++){
            $user = $visits[$i]->user;
            $date = new DateTime($visits[$i]->date);
            $dateTime = new DateTime();
            $timestamp1 = $date->getTimestamp();
            $timestamp2 = $dateTime->getTimestamp();
            $diffInSeconds = $timestamp1 - $timestamp2;
            $minutesDifference = floor($diffInSeconds / 60);
            if($minutesDifference<=$user->notification && !$visits[$i]->notified){
                $visits[$i]->notify();
                $visitsGroup = $visits[$i]->visits;
                for($j=0;$j<count($visitsGroup);$j++){
                    $visitsGroup[$j]->notify();
                }
                $not = $user->notification;
                $token = "FdhwGf65s8Jsth1yrWo2TvvvwhgMxG4IrLo5XKwy";
                $godzina = $dateTime->format('Y-m-d H:i');
                $mes = 'Twoja wizyta w KBF Barber Shop odbedzie sie za '.$minutesDifference.' minut, o godzinie '. $godzina;
                if($not>30){
                    $not/=60;
                    $mes = 'Twoja wizyta w KBF Barber Shop odbedzie sie za '.$not.'h, o godzinie '. $godzina;
                }
                $params = array(
                    'to' => $user->phone,
                    'from' => 'Test',
                    'message' => $mes,
                    'format' => 'json'
                );
                SendSMS::sms_send($params, $token);
            }
        }
        return [
            'error'=>FALSE,
            'message'=>NULL
        ];
    }
}