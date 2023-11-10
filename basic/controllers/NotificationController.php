<?php

namespace app\controllers;

use app\models\Ban;
use app\models\Code;
use app\models\Price;
use app\models\SendSMS;
use app\models\Type;
use app\models\User;
use app\models\Visit;
use PhpParser\Node\Expr\Print_;
use Symfony\Component\Finder\Finder;
use Yii;
use yii\base\ViewEvent;
use yii\db\Expression;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;
use function PHPUnit\Framework\stringContains;

class NotificationController extends \app\components\Controller
{
    public function actionSendnoti(){
        $visits = Visit::find()->where(['>', 'STR_TO_DATE(date, "%d-%m-%Y %H:%i")', new Expression('NOW()')])->all();

        return $visits;
    }
}