<?php

namespace app\models;

use yii\db\Expression;
use app\models\Visit;

class Notification
{
    public function SendNotification(){
        $visits = Visit::find()->where(['date' => "adam"])->all();
        return $visits;
    }
}
