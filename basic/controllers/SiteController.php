<?php

namespace app\controllers;

use app\models\AdditionalServices;
use app\models\AdditionalType;
use app\models\Ban;
use app\models\Barber;
use app\models\Code;
use app\models\Message;
use app\models\Price;
use app\models\PushNotification;
use app\models\SendSMS;
use app\models\Type;
use app\models\User;
use app\models\Visit;
use app\models\VisitAdditional;
use Cassandra\Date;
use ExpoSDK\Expo;
use ExpoSDK\ExpoMessage;
use PhpParser\Node\Expr\Print_;
use Symfony\Component\Finder\Finder;
use Yii;
use yii\base\ViewEvent;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;
use function PHPUnit\Framework\stringContains;
use const Grpc\CHANNEL_TRANSIENT_FAILURE;

class SiteController extends \app\components\Controller
{
    /**
     * {@inheritdoc}
     */
    /*public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => ['logout'],
                'rules' => [
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }*/

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        return $this->render('index');
    }
    public function actionRegister(){
        $post = $this->getJsonInput();
        $user = new User();
        if (isset($post->password)) {
            $user->password = $post->password;
        }
        if (isset($post->name)) {
            $user->name = $post->name;
        }
        if (isset($post->last_name)) {
            $user->last_name = $post->last_name;
        }
        if (isset($post->phone)) {
            $user->phone = 48;
            $user->phone.=$post->phone;
            $user2 = User::find()->andWhere(['phone'=>$user->phone])->one();
            if($user2){
                return [
                    'error' => true,
                    'message' => 'taki numer juz istnieje'
                ];
            }
            if(strlen($user->phone)>11){
                return [
                    'error' => true,
                    'message' => 'nie poprawny format numeru',
                ];
            }
        }
        if ($user->validate()) {
            $user->save();
            return [
                'error' => FALSE,
                'message' => NULL,
            ];
        } else {
            return [
                'error' => true,
                'message' => $user->getErrorSummary(false),
            ];
        }
    }
    public function actionAddvisit(){
        $post = $this->getJsonInput();
        $user = Yii::$app->user->identity;
        if(!$user->verified){
            return [
                'error' => true,
                'message' => 'ten uzytkownik nie jest zweryfikowany',
            ];
        }
        $barber = Barber::find()->andWhere(['id'=>$post->barber_id])->one();
        if(!isset($barber)){
            return [
                'error' => true,
                'message' => 'taki barber nie istnieje',
            ];
        }
        $type = Type::find()->andWhere(['id'=>$post->type_id])->one();
        $visitNumber = $type->time;
        for($i=0;$i<count($post->additions);$i++){
            $add = AdditionalServices::find()->andWhere(['id'=>$post->additions[$i]->additional_id])->one();
            $visitNumber += $add->time;
        }
        $visitNumber/=15;
        $startMinute = 0;
        for($i=0;$i<$visitNumber;$i++){
            $startMinute = $i * 15;
            $visitDate = new \DateTime($post->date);
            $visitDate = $visitDate->modify('+' . $startMinute . ' minutes');
            $v = Visit::find()->andWhere(['date'=>$visitDate->format('Y-m-d H:i')])->andWhere(['barber_id'=>$barber->id])->one();
            $postMin = $visitDate->format('i');
            if ($postMin != '00' && $postMin != '15' && $postMin != '30' && $postMin != '45') {
                return [
                    'error' => true,
                    'message_user' => 'data jest w niepoprawnym formacie',
                ];
            }
            if(isset($v) || !$barber->validateHour($visitDate)){
                return [
                    'error' => true,
                    'message_user' => 'nie mozna utworzyc wizyty w tym czasie',
                ];
            }
        }
        $groupVisit = null;
        $startMinute = 0;
        for($i=0;$i<$visitNumber;$i++) {
            $startMinute = $i * 15;
            $visit = new Visit();
            if (isset($post->date)) {
                $visit->date = $post->date;
            }
            if (isset($post->barber_id)) {
                $visit->barber_id = $post->barber_id;
            }
            if (isset($post->type_id)) {
                $visit->type_id = $post->type_id;
            }
            if (isset($post->additional_info)) {
                $visit->additional_info = $post->additional_info;
            }
            $visit->user_id = $user->id;
            if($visit->validate()){
                if($i!=0){
                    $visit->group = $groupVisit->id;
                    $visDate = new \DateTime($visit->date);
                    $visDate = $visDate->modify('+' . $startMinute . ' minutes');
                    $visit->date = $visDate->format('Y-m-d H:i');
                }
                $visit->save();
                if($i==0) {
                    $groupVisit = $visit;
                    for ($j = 0; $j < count($post->additions); $j++) {
                        $add = new VisitAdditional();
                        $add->visit_id = $visit->id;
                        $add->additional_id = $post->additions[$j]->additional_id;
                        if ($add->validate()) {
                            $add->save();
                        } else {
                            return [
                                'error' => true,
                                'message' => $add->getErrorSummary(false),
                            ];
                        }
                    }
                }
            } else {
                return [
                    'error' => true,
                    'message' => $visit->getErrorSummary(false),
                ];
            }
        }
        return [
            'error' => false,
            'message' => null,
        ];
    }
    public function actionGetvisits($barber_id){
        $post = $this->getJsonInput();
        $user = Yii::$app->user->identity;
        if(!$user->verified){
            return [
                'error' => true,
                'message' => 'ten uzytkownik nie jest zweryfikowany',
            ];
        }
        if(isset($post->date)){
            $day = $post->date;
        }
        if(!isset($day)){
            return[
                'error'=>TRUE,
                'message'=>'Date is required'
            ];
        }
        $visit = Visit::find()->andWhere(['barber_id'=>$barber_id])->all();
        $barber = Barber::find()->andWhere(['id'=>$barber_id])->one();
        $visits = array();
        $minutes =0;
        $hStartTimestamp = strtotime('1970-01-01 ' . $barber->hour_start);
        $hEndTimestamp = strtotime('1970-01-01 ' . $barber->hour_end);
        $hours=$hStartTimestamp/3600;
        $iterations =(($hEndTimestamp/3600)-($hStartTimestamp/3600))/0.25;
        for($i=0;$i<=$iterations;$i++) {
            $string = "0";
            if ($minutes == 60) {
                $hours++;
                $minutes = 0;
            }
            $visits[] = [
                'date' => $day . " " . $hours . ":" . $minutes,
                'status' => 0,
                'date_end' => null
            ];
            if ($minutes == 0) $visits[$i]['date'] .= $string;
            $minutes += 15;
        }
        for($i=0;$i<count($visits);$i++) {
            for($j=0;$j<count($visit);$j++) {
                if (str_contains($visits[$i]['date'], $visit[$j]['date'])) {
                    $visits[$i]['status'] = 1;
                    $dateTime = new \DateTime($visit[$j]->date);
                    $dateTime->modify('+15 minutes');
                    $visits[$i]['date_end'] = $dateTime->format('Y-m-d H:i');
                }
            }
        }
        return [
            'error' => FALSE,
            'message' => NULL,
            'visit' => $visits
        ];
    }
    public function actionGetuservisit(){
        $user = Yii::$app->user->identity;
        if(!$user->verified){
            return [
                'error' => true,
                'message' => 'ten uzytkownik nie jest zweryfikowany',
            ];
        }
        $visit = Visit::find()->andWhere(['user_id'=>$user->id])->andWhere(['group'=>null])->all();
        $visits = array();
        for($i=0;$i<count($visit);$i++){
            $barber = $visit[$i]->barber;
            $type = $visit[$i]->type;
            $additional = $visit[$i]->visitAdditionals;
            $price = $type->price;
            $time = $type->time;
            for($j=0;$j<count($additional);$j++){
                $add = $additional[$j]->additional;
                $price+=$add->price;
                $time+=$add->time;
            }
            $visits[] = [
                'id'=>$visit[$i]->id,
                'date'=>$visit[$i]->date,
                'barber_name'=>$barber->name,
                'barber_last_name'=>$barber->last_name,
                'img_url'=>$barber->img_url,
                'label'=>$type->label,
                'additional_info'=>$visit[$i]->additional_info,
                'user_id'=>$visit[$i]->user_id,
                'notified'=>$visit[$i]->notified,
                'price'=>$price,
                'time'=>$time,
            ];
        }
        return [
            'error' => FALSE,
            'message' => NULL,
            'visit' => $visits
        ];
    }
    public function actionChangeuserdata(){
        $user = Yii::$app->user->identity;
        if(!$user->verified){
            return [
                'error' => true,
                'message' => 'ten uzytkownik nie jest zweryfikowany',
            ];
        }
        $post = $this->getJsonInput();
        $name = null;
        $last_name = null;
        $phone = null;
        if(isset($post->name)){
            $name = $post->name;
        }
        else{
            $name = $user->name;
        }
        if(isset($post->last_name)){
            $last_name = $post->last_name;
        }
        else {
            $last_name = $user->last_name;
        }
        if(isset($post->phone)){
            $ph = 48;
            $ph.=$post->phone;
            if(strlen($ph)>11){
                return [
                    'error' => true,
                    'message' => 'nie poprawny format numeru',
                ];
            }
            $usr = User::find()->andWhere(['phone'=>$ph])->one();
            if($usr != null){
                return [
                    'error' => true,
                    'message' => 'taki numer juz istnieje',
                ];
            }
            $phone = $post->phone;
        }
        else {
            $phone = $user->phone;
        }
        $user->changeData($name, $last_name, $phone);
        return [
            'error' => FALSE,
            'message' => NULL,
        ];
    }
    public function actionDeletevisit(){
        $user = Yii::$app->user->identity;
        if(!$user->verified){
            return [
                'error' => true,
                'message_user' => 'ten uzytkownik nie jest zweryfikowany',
            ];
        }
        $post = $this->getJsonInput();
        $visit = Visit::find()->andWhere(['id' => $post->visit_id])->one();
        if(is_null($visit)){
            return [
                'error' => TRUE,
                'message' => "nie ma takiej wizyty",
            ];
        }
        if($visit->user_id!=$user->id && $user->admin==0){
            return [
                'error' => TRUE,
                'message' => "ta wizyta nie nalezy do ciebie, lub nie jestes adminem",
            ];
        }
        if($visit->group == null){
            $visit->delete();
        }
        else {
            $visitGroup = Visit::find()->andWhere(['id'=>$visit->group])->one();
            $visit = $visitGroup;
            $userVisit = $visit->user;
            $mes = new ExpoMessage();
            $mes->setBody('Twoja wizyta o godzinie '.$visitGroup->date.' zostala odwolana');
            $mes->setTitle('Odwolana wizyta');
            PushNotification::pushNoti($userVisit->notification_token, $mes);
            $visitGroup->delete();
        }
        if($user->admin==1 && $visit->user!=$user){
            $token = "FdhwGf65s8Jsth1yrWo2TvvvwhgMxG4IrLo5XKwy";
            $userVisit = $visit->user;
            $mes = new ExpoMessage();
            $mes->setBody('Twoja wizyta o godzinie '.$visit->date.' zostala odwolana');
            $mes->setTitle('Odwolana wizyta');
            PushNotification::pushNoti($userVisit->notification_token, $mes);
            $params = array(
                'to' => $userVisit->phone,
                'from' => 'Test',
                'message' => 'Twoja wizyta o godzinie '.$visit->date.' zostala odwolana',
                'format' => 'json'
            );
            return $params;
            SendSMS::sms_send($params, $token);
        }
        return [
            'error' => FALSE,
            'message' => null,
        ];
    }
    public function actionBanuser($phone){
        $user = Yii::$app->user->identity;
        if($user->admin==0){
            return [
                'error' => TRUE,
                'message' => 'you are not an admin',
            ];
        }
        $ban = User::find()->andWhere(['phone'=>$phone])->one();
        if(is_null($ban)){
            return [
                'error' => TRUE,
                'message' => 'This user does not exist',
            ];
        }
        $ban->ban();
        return [
            'error' => FALSE,
            'message' => null,
        ];
    }
    public function actionUnbanuser($phone){
        $user = Yii::$app->user->identity;
        if($user->admin==0){
            return [
                'error' => TRUE,
                'message' => 'you are not an admin',
            ];
        }
        $banUser = User::find()->andWhere(['phone'=>$phone])->one();
        if(is_null($banUser)){
            return [
                'error' => TRUE,
                'message' => 'This user does not exist',
            ];
        }
        $banUser->unban();
        return [
            'error' => FALSE,
            'message' => null,
        ];
    }
    public function actionDayoff(){
        $user = Yii::$app->user->identity;
        if($user->admin==0){
            return [
                'error' => TRUE,
                'message' => 'you are not an admin',
            ];
        }
        $mes = new ExpoMessage();
        $post = $this->getJsonInput();
        if(!isset($post->date)){
            return[
                'error'=>TRUE,
                'message'=>'Date is required'
            ];
        }
        $date = $post->date;
        $visitDate = new \DateTime($date);
        $postMin = $visitDate->format('i');
        if ($postMin != '00' && $postMin != '15' && $postMin != '30' && $postMin != '45') {
            return [
                'error' => true,
                'message_user' => 'data jest w niepoprawnym formacie',
            ];
        }
        $test = array();
        $allDay = false;
        if(strlen($date)<11){
            $allDay=true;
        }
        $visits = array();
        $minutes =0;
        $hours=9;
        $barber = Barber::find()->andWhere(['user_id'=>$user->id])->one();
        $token = "FdhwGf65s8Jsth1yrWo2TvvvwhgMxG4IrLo5XKwy";
        if($allDay) {
            $visits = Visit::find()->andWhere(['like','date', $date])->all();
            for($i=0;$i<count($visits);$i++){
                $user = $visits[$i]->user;
                $liczba = 0;
                if($visits[$i]->group==null){
                    $mes->setBody('Twoja wizyta o godzinie '.$visits[$i]->date.' zostala odwolana');
                    $mes->setTitle('Odwolana wizyta');
                    PushNotification::pushNoti($user->notification_token, $mes);
                    $params = array(
                        'to' => $user->phone,
                        'from' => 'Test',
                        'message' => 'Twoja wizyta o godzinie '.$visits[$i]->date.' zostala odwolana',
                        'format' => 'json'
                    );
                    SendSMS::sms_send($params, $token);
                    $visits[$i]->delete();
                }
                else if($visits[$i]->group!=null){
                    $visitGroup = $visits[$i];
                    $mes->setBody('Twoja wizyta o godzinie '.$visitGroup->date.' zostala odwolana');
                    $mes->setTitle('Odwolana wizyta');
                    PushNotification::pushNoti($user->notification_token, $mes);
                    $params = array(
                        'to' => $user->phone,
                        'from' => 'Test',
                        'message' => 'Twoja wizyta o godzinie '.$visitGroup->date.' zostala odwolana',
                        'format' => 'json'
                    );
                    SendSMS::sms_send($params, $token);
                    $visitGroup->delete();
                }
            }
            $hStartTimestamp = strtotime('1970-01-01 ' . $barber->hour_start);
            $hEndTimestamp = strtotime('1970-01-01 ' . $barber->hour_end);
            $hours=$hStartTimestamp/3600;
            $iterations =(($hEndTimestamp/3600)-($hStartTimestamp/3600))/0.25;
            $dayoff = Type::find()->andWhere(['label'=>'dayoff'])->one();
            $dates = array();
            for ($i = 0; $i <= $iterations; $i++) {
                $string = "0";
                if ($minutes == 60) {
                    $hours++;
                    $minutes = 0;
                }
                $dates[] = $date . " " . $hours . ":" . $minutes;
                if ($minutes == 0) $dates[$i] .= $string;
                $minutes += 15;
            }
            for ($i = 0; $i <= $iterations; $i++) {
                $visit = new Visit();
                $visit->date = $dates[$i];
                $visit->barber_id = $barber->id;
                $visit->user_id = $user->id;
                $visit->type_id = $dayoff->id;
                if ($visit->validate()) {
                    $visit->save();
                } else {
                    return [
                        'error' => true,
                        'message' => $visit->getErrorSummary(false),
                    ];
                }
            }
        }
        else{
            $dayoff = Type::find()->andWhere(['label'=>'dayoff'])->one();
            $oldVisit = Visit::find()->andWhere(['date'=>$date])->one();
            $user = $oldVisit->user;
            if($oldVisit!=null){
                if($oldVisit->group==null){
                    $mes->setBody('Twoja wizyta o godzinie '.$oldVisit->date.' zostala odwolana');
                    $mes->setTitle('Odwolana wizyta');
                    PushNotification::pushNoti($user->notification_token, $mes);
                    $params = array(
                        'to' => $user->phone,
                        'from' => 'Test',
                        'message' => 'Twoja wizyta o godzinie '.$oldVisit->date.' zostala odwolana',
                        'format' => 'json'
                    );
                    SendSMS::sms_send($params, $token);
                    $oldVisit->delete();
                }
                else if($oldVisit->group!=null){
                    $visitGroup = $oldVisit->group0;
                    $mes->setBody('Twoja wizyta o godzinie '.$visitGroup->date.' zostala odwolana');
                    $mes->setTitle('Odwolana wizyta');
                    PushNotification::pushNoti($user->notification_token, $mes);
                    $params = array(
                        'to' => $user->phone,
                        'from' => 'Test',
                        'message' => 'Twoja wizyta o godzinie '.$visitGroup->date.' zostala odwolana',
                        'format' => 'json'
                    );
                    SendSMS::sms_send($params, $token);
                    $visitGroup->delete();
                }
            }
            $visit = new Visit();
            $visit->date = $date;
            $visit->barber_id = $barber->id;
            $visit->user_id = $user->id;
            $visit->type_id = $dayoff->id;
            if ($visit->validate()) {
                $visit->save();
            } else {
                return [
                    'error' => true,
                    'message' => $visit->getErrorSummary(false),
                ];
            }
        }
        return [
            'error' => FALSE,
            'message' => NULL
        ];
    }
    public function actionUserdata(){
        $user = Yii::$app->user->identity;
        if(!$user->verified){
            return [
                'error' => true,
                'message_user' => 'ten uzytkownik nie jest zweryfikowany',
            ];
        }
        return[
            'error' => FALSE,
            'message' => NULL,
            'name'=>$user->name,
            'last_name'=>$user->last_name,
            'phone'=>$user->phone,
            'notification'=>$user->notification
        ];
    }
    public function actionBanedusers(){
        $user = Yii::$app->user->identity;
        if($user->admin==0){
            return [
                'error' => TRUE,
                'message' => 'you are not an admin',
            ];
        }
        $ban = User::find()->andWhere(['ban'=>1])->all();
        return [
            'error' => FALSE,
            'message' => NULL,
            'users' => $ban
        ];
    }
    public function actionCloseacc(){
        $user = Yii::$app->user->identity;
        $user->delete();
        return [
            'error' => FALSE,
            'message' => NULL,
        ];
    }
    public function actionGettypes(){
        $types = Type::find()->andWhere(['<>', 'label', 'dayoff'])->all();
        $res = array();
        for($i=0;$i<count($types);$i++){
            $additional = AdditionalType::find()->andWhere(['type_id' => $types[$i]->id])->all();
            $radio = array();
            $checkbox = array();
            for($j=0;$j<count($additional);$j++){
                $services = AdditionalServices::find()->andWhere(['id'=>$additional[$j]->additional_id])->all();
                for($a=0;$a<count($services);$a++){
                    if($services[$a]->radio){
                        $radio[] = $services[$a];
                    }
                    else{
                        $checkbox[] = $services[$a];
                    }
                }
            }
            $res[] =[
                'type' => $types[$i],
                'additions' => ['radio' => $radio, 'checkbox' => $checkbox]
            ];
        }
        return [
            'error' => FALSE,
            'message' => NULL,
            'types' => $res
        ];
    }
    public function actionChangetype(){
        $user = Yii::$app->user->identity;
        if($user->admin==0){
            return [
                'error' => TRUE,
                'message' => 'you are not an admin',
            ];
        }
        $post = $this->getJsonInput();
        $type = null;
        if(isset($post->type_id)){
            $type = Type::find()->andWhere(['id'=>$post->type_id])->one();
        }
        else if(isset($post->additional_id)){
            $type = AdditionalServices::find()->andWhere(['id'=>$post->additional_id])->one();
        }
        $label = $type->label;
        $price = $type->price;
        $time = $type->time;
        if(isset($post->label)){
            $label = $post->label;
        }
        if(isset($post->price)){
            $price = $post->price;
        }
        if(isset($post->time)){
            $time = $post->time;
        }
        $type->changeType($label, $time, $price);
        return [
            'error' => FALSE,
            'message' => NULL,
        ];
    }
    public function actionAddtype(){
        $user = Yii::$app->user->identity;
        if($user->admin==0){
            return [
                'error' => TRUE,
                'message' => 'you are not an admin',
            ];
        }
        $post = $this->getJsonInput();
        $additional = false;
        if(isset($post->additional)){
            if($post->additional) $additional= true;
        }
        if($additional){
            $add = new AdditionalServices();
            if(isset($post->label)){
                $add->label = $post->label;
            }
            if(isset($post->price)){
                if($post->price%15!=0){
                    return [
                        'error' => TRUE,
                        'message' => 'niepoprawny format czasu',
                    ];
                }
                $add->price = $post->price;
            }
            if(isset($post->time)){
                $add->time = $post->time;
            }
            if($add->validate()){
                $add->save();
                return [
                    'error' => false,
                    'message' => null,
                ];
            }
            else{
                return [
                    'error' => TRUE,
                    'message' => $add->getErrorSummary(false),
                ];
            }
        }
        $type = new Type();
        if(isset($post->label)){
            $type->label = $post->label;
        }
        if(isset($post->price)){
            if($post->price%15!=0){
                return [
                    'error' => TRUE,
                    'message' => 'niepoprawny format czasu',
                ];
            }
            $type->price = $post->price;
        }
        if(isset($post->time)){
            $type->time = $post->time;
        }
        if($type->validate()){
            $type->save();
            return [
                'error' => FALSE,
                'message' => NULL,
            ];
        }
        else {
            return [
                'error' => TRUE,
                'message' => $type->getErrorSummary(false),
            ];
        }
    }
    public function actionVerificateacc(){
        $post = $this->getJsonInput();
        if(!isset($post->code)){
            return [
                'error' => TRUE,
                'message' => 'code is required',
            ];
        }
        $code = Code::find()->andWhere(['code'=>$post->code])->one();
        if(is_null($code)){
            return [
                'error' => TRUE,
                'message' => 'code is incorrect',
            ];
        }
        $user = $code->user;
        if(is_null($user)){
            return [
                'error' => TRUE,
                'message' => 'code is incorrect',
            ];
        }
        $user->verify();
        $code->delete();
        return [
            'error' => FALSE,
            'message' => NULL,
        ];
    }
    public function actionChangepass(){
        $user = Yii::$app->user->identity;
        $post = $this->getJsonInput();
        if(!isset($post->code)){
            return [
                'error' => TRUE,
                'message' => 'code is required',
            ];
        }
        $code = Code::find()->andWhere(['code'=>$post->code])->one();
        if(!isset($code)){
            return [
                'error' => TRUE,
                'message' => 'code is incorrect',
            ];
        }
        $user->changePassword($post->password);
        $code->delete();
        return [
            'error' => FALSE,
            'message' => NULL,
        ];
    }
    public function actionSendsms(){
        $user = Yii::$app->user->identity;
        $code = new Code();
        $code->code = rand(1000, 9999);
        $code->user_id = $user->id;
        $code->save();
        $token = "FdhwGf65s8Jsth1yrWo2TvvvwhgMxG4IrLo5XKwy";
        $params = array(
            'to' => $user->phone,
            'from' => 'Test',
            'message' => $code->code,
            'format' => 'json'
        );
        SendSMS::sms_send($params, $token);
        return [
            'error' => FALSE,
            'message' => NULL,
        ];
    }
    public function actionSmsforpassword(){
        $post = $this->getJsonInput();
        if(!isset($post->phone)){
            return [
                'error' => TRUE,
                'message' => 'phone is required',
            ];
        }
        $user = User::find()->andWhere(['phone'=>$post->phone])->one();
        if(!isset($post->phone)){
            return [
                'error' => TRUE,
                'message' => 'incorrect phone',
            ];
        }
        $code = new Code();
        $code->code = rand(1000, 9999);
        $code->user_id = $user->id;
        $code->save();
        $token = "FdhwGf65s8Jsth1yrWo2TvvvwhgMxG4IrLo5XKwy";
        $params = array(
            'to' => $user->phone,
            'from' => 'Test',
            'message' => $code->code,
            'format' => 'json'
        );
        SendSMS::sms_send($params, $token);
        return [
            'error' => FALSE,
            'message' => NULL,
        ];
    }
    public function actionChangenot(){
        $user = Yii::$app->user->identity;
        $post = $this->getJsonInput();
        if(!isset($post->notification)){
            return [
                'error' => TRUE,
                'message' => 'notification parameter is required',
            ];
        }
        $user->changeNoti($post->notification);
        return [
            'error' => FALSE,
            'message' => NULL,
        ];
    }
    public function actionDeletetype(){
        $user = Yii::$app->user->identity;
        if($user->admin==0){
            return [
                'error' => TRUE,
                'message' => 'you are not an admin',
            ];
        }
        $post = $this->getJsonInput();
        if(!isset($post->type_id)){
            return [
                'error' => TRUE,
                'message' => 'type_id is required',
            ];
        }
        $type = Type::find()->andWhere(['id'=>$post->type_id])->one();
        if(is_null($post->type_id)){
            return [
                'error' => TRUE,
                'message' => 'this type does not exist',
            ];
        }
        if($type->label == 'dayoff'){
            return [
                'error' => TRUE,
                'message' => 'nie mozna usunac tego typu',
            ];
        }
        $type->delete();
        return [
            'error' => FALSE,
            'message' => NULL,
        ];
    }
    public function actionDayon(){
        $user = Yii::$app->user->identity;
        if($user->admin==0){
            return [
                'error' => TRUE,
                'message' => 'you are not an admin',
            ];
        }
        $post = $this->getJsonInput();
        if(!isset($post->date)){
            return[
                'error'=>TRUE,
                'message'=>'Date is required'
            ];
        }
        $formattedDate = \DateTime::createFromFormat('Y-m-d', $post->date)->format('Y-m-d');
        $dayoff = Type::find()->andWhere(['label'=>'dayoff'])->one();
        Visit::deleteAll("date LIKE '$formattedDate%' AND type_id = $dayoff->id");
        return [
            'error' => FALSE,
            'message' => NULL,
        ];
    }
    public function actionUpdatevisit(){
        $user = Yii::$app->user->identity;
        if(!$user->verified){
            return [
                'error' => true,
                'message' => 'ten uzytkownik nie jest zweryfikowany',
            ];
        }
        $post = $this->getJsonInput();
        if(!isset($post->visit_id)){
            return [
                'error' => true,
                'message' => 'id wizyty jest wymagane',
            ];
        }
        $visit = Visit::find()->andWhere(['id'=>$post->visit_id])->one();
        if(!$user->admin && $visit->user!=$user){
            return [
                'error' => true,
                'message' => 'nie mozesz edytowac tej wizyty',
            ];
        }
        $visits = null;
        if(!is_null($visit->group)){
            $visits = Visit::find()->andWhere(['group'=>$visit->group])->all();
            $visit = Visit::find()->andWhere(['id'=>$visits[0]->group])->one();
        }
        else if(is_null($visit->group)){
            $visits = $visit->visits;
        }
        for($i=0;$i<count($visits);$i++){
            $visits[$i]->updateVisit($post->additional_info);
        }
        $visit->updateVisit($post->additional_info);
        return [
            'error' => FALSE,
            'message' => NULL,
        ];
    }
    public function actionGetbarbers(){
        $user = Yii::$app->user->identity;
        if(!$user->verified){
            return [
                'error' => true,
                'message' => 'ten uzytkownik nie jest zweryfikowany',
            ];
        }
        $barbers = Barber::find()->all();
        return [
            'error' => false,
            'message' => null,
            'barbers' => $barbers
        ];
    }
    public function actionSendmessage(){
        $user = Yii::$app->user->identity;
        if(!$user->verified){
            return [
                'error' => true,
                'message' => 'ten uzytkownik nie jest zweryfikowany',
            ];
        }
        $post = $this->getJsonInput();
        $barber = null;
        if(!isset($post->message)){
            return [
                'error' => true,
                'message' => 'wiadomosc jest wymagana',
            ];
        }
        $message = $post->message;
        $barber_user = null;
        $mes = new Message();
        $mes->message = $message;
        $mes->date = \date('Y-m-d H:i');
        if(isset($post->barber_id)){
            $barber = Barber::find()->andWhere(['id'=>$post->barber_id])->one();
            $mes->user_id = $user->id;
            $mes->barber_id = $barber->id;
            $barber_user = $barber->user;
            $mes->from = "user";
            $mes->user_readed = 1;
        }
        else if(isset($post->user_id)){
            $barber = Barber::find()->andWhere(['user_id'=>$user->id])->one();
            $mes->user_id = $post->user_id;
            $mes->barber_id = $barber->id;
            $mes->barber_readed = 1;
            $mes->from = "barber";
            $barber_user = User::find()->andWhere(['id'=>$post->user_id])->one();
        }

        if(!isset($post->message)){
            return [
                'error' => true,
                'message' => 'tresc wiadomosci jest wymagana',
            ];
        }
        if($mes->validate()){
            $mes->save();
            $messages = [
                new ExpoMessage([
                    'title' => 'Nowa wiadomosc od '.$user->name.' '.$user->last_name,
                    'body' => $mes->message,
                ]),
            ];
            if(isset($barber_user->notification_token)) {
                $defaultRecipients[] = $barber_user->notification_token;
                (new Expo)->send($messages)->to($defaultRecipients)->push();
            }
            return [
                'error' => false,
                'message' => null,
            ];
        }
        else{
            return [
                'error' => true,
                'message' => $mes->getErrorSummary(false),
            ];
        }
    }
    public function actionGetchat(){
        $user = Yii::$app->user->identity;
        if(!$user->verified){
            return [
                'error' => true,
                'message' => 'ten uzytkownik nie jest zweryfikowany',
            ];
        }
        $post = $this->getJsonInput();
        $chat = null;
        if(isset($post->barber_id)){
            $chat = Message::find()->andWhere(['user_id'=>$user->id])->andWhere(['barber_id'=>$post->barber_id])->all();
        }
        else if(isset($post->user_id)){
            $barber = Barber::find()->andWhere(['user_id'=>$user->id])->one();
            $chat = Message::find()->andWhere(['user_id'=>$post->user_id])->andWhere(['barber_id'=>$barber->id])->all();
        }
        for($i=0;$i<count($chat);$i++){
            if(isset($post->user_id)){
                $chat[$i]->readBarber();
            }
            else{
                $chat[$i]->readUser();
            }
        }
        return [
            'error' => false,
            'message' => null,
            'chat'=>$chat
        ];
    }
    public function actionGetchats(){
        $user = Yii::$app->user->identity;
        if(!$user->verified){
            return [
                'error' => true,
                'message' => 'ten uzytkownik nie jest zweryfikowany',
            ];
        }
        $isBarber = false;
        $barber = Barber::find()->andWhere(['user_id'=>$user->id])->one();
        if(!is_null($barber)) $isBarber = true;
        if($isBarber){
            $messages = Message::find()->andWhere(['barber_id'=>$barber->id])->all();
            $chats = array();
            for($i=count($messages);$i>=0;$i--){
                if($i==count($messages)){
                    $userM = $messages[$i-1]->user;
                    $chats[] = [
                        'user_id'=>$userM->id,
                        'last_message' => $messages[$i-1]->message,
                        'name' => $userM->name,
                        'last_name' => $userM->last_name,
                        'date'=>$messages[$i-1]->date,
                        'readed'=>$messages[$i-1]->barber_readed
                    ];
                }
                else if(!in_array($messages[$i]->user_id, array_column($chats, 'user_id'))){
                    $userM = $messages[$i]->user;
                    $chats[] = [
                        'user_id'=>$userM->id,
                        'last_message' => $messages[$i]->message,
                        'name' => $userM->name,
                        'last_name' => $userM->last_name,
                        'date'=>$messages[$i]->date,
                        'readed'=>$messages[$i]->barber_readed
                    ];
                }
            }
            return [
                'error' => false,
                'message'=> null,
                'chats' => $chats
            ];
        }
        else {
            $messages = Message::find()->andWhere(['user_id'=>$user->id])->all();
            $chats = array();
            for($i=count($messages);$i>=0;$i--){
                if($i==count($messages)){
                    $userB = $messages[$i-1]->barber;
                    $chats[] = [
                        'barber_id'=>$userB->id,
                        'last_message' => $messages[$i-1]->message,
                        'name' => $userB->name,
                        'last_name' => $userB->last_name,
                        'date'=>$messages[$i-1]->date,
                        'readed'=>$messages[$i-1]->user_readed
                    ];
                }
                else if(!in_array($messages[$i]->barber_id, array_column($chats, 'barber_id'))){
                    $userB = $messages[$i]->barber;
                    $chats[] = [
                        'barber_id'=>$userB->id,
                        'last_message' => $messages[$i]->message,
                        'name' => $userB->name,
                        'last_name' => $userB->last_name,
                        'date'=>$messages[$i]->date,
                        'readed'=>$messages[$i]->user_readed
                    ];
                }
            }
            return [
                'error' => false,
                'message'=> null,
                'chats' => $chats
            ];
        }
    }
    public function actionAddvisitadmin(){
        $user = Yii::$app->user->identity;
        if(!$user->verified){
            return [
                'error' => true,
                'message' => 'ten uzytkownik nie jest zweryfikowany',
            ];
        }
        if(!$user->admin){
            return [
                'error' => true,
                'message' => 'ten uzytkownik nie jest administratorem',
            ];
        }
        $post = $this->getJsonInput();
        $barber = Barber::find()->andWhere(['user_id'=>$user->id])->one();
        if(!isset($barber)){
            return [
                'error' => true,
                'message' => 'taki barber nie istnieje',
            ];
        }
        $type = Type::find()->andWhere(['id'=>$post->type_id])->one();
        $visitNumber = $type->time;
        for($i=0;$i<count($post->additions);$i++){
            $add = AdditionalServices::find()->andWhere(['id'=>$post->additions[$i]->additional_id])->one();
            $visitNumber += $add->time;
        }
        $visitNumber/=15;
        $startMinute = 0;
        for($i=0;$i<$visitNumber;$i++){
            $startMinute = $i * 15;
            $visitDate = new \DateTime($post->date);
            $visitDate = $visitDate->modify('+' . $startMinute . ' minutes');
            $v = Visit::find()->andWhere(['date'=>$visitDate->format('Y-m-d H:i')])->andWhere(['barber_id'=>$barber->id])->one();
            $postMin = $visitDate->format('i');
            if ($postMin != '00' && $postMin != '15' && $postMin != '30' && $postMin != '45') {
                return [
                    'error' => true,
                    'message_user' => 'data jest w niepoprawnym formacie',
                ];
            }
            if(isset($v) || !$barber->validateHour($visitDate)){
                return [
                    'error' => true,
                    'message_user' => 'nie mozna utworzyc wizyty w tym czasie',
                ];
            }
        }
        $groupVisit = null;
        $startMinute = 0;
        for($i=0;$i<$visitNumber;$i++) {
            $startMinute = $i * 15;
            $visit = new Visit();
            if (isset($post->date)) {
                $visit->date = $post->date;
            }
            if (isset($post->barber_id)) {
                $visit->barber_id = $post->barber_id;
            }
            if (isset($post->type_id)) {
                $visit->type_id = $post->type_id;
            }
            if (isset($post->additional_info)) {
                $visit->additional_info = $post->additional_info;
            }
            $visit->user_id = $user->id;
            if($visit->validate()){
                if($i!=0){
                    $visit->group = $groupVisit->id;
                    $visDate = new \DateTime($visit->date);
                    $visDate = $visDate->modify('+' . $startMinute . ' minutes');
                    $visit->date = $visDate->format('Y-m-d H:i');
                }
                $visit->save();
                if($i==0) {
                    $groupVisit = $visit;
                    for ($j = 0; $j < count($post->additions); $j++) {
                        $add = new VisitAdditional();
                        $add->visit_id = $visit->id;
                        $add->additional_id = $post->additions[$j]->additional_id;
                        if ($add->validate()) {
                            $add->save();
                        } else {
                            return [
                                'error' => true,
                                'message' => $add->getErrorSummary(false),
                            ];
                        }
                    }
                }
            } else {
                return [
                    'error' => true,
                    'message' => $visit->getErrorSummary(false),
                ];
            }
        }
        return [
            'error' => false,
            'message' => null,
        ];
    }
    public function actionCreateuser(){
        $user = Yii::$app->user->identity;
        if($user->admin==0){
            return [
                'error' => TRUE,
                'message' => 'you are not an admin',
            ];
        }
        $post = $this->getJsonInput();
        $phone = 48;
        $phone .= $post->phone;
        $oldUser = User::find()->andWhere(['phone' => $phone])->one();
        if($oldUser){
            return [
                'error' => TRUE,
                'message' => 'uzytkownik z takim numerem telefonu juz istnieje',
            ];
        }
        $newUser = new User();
        $newUser->name = $post->name;
        $newUser->last_name = $post->last_name;
        $newUser->phone = 48;
        $newUser->phone .= $post->phone;
        $newUser->password = $post->password;
        $newUser->verified = 1;
        if($newUser->validate()){
            $newUser->save();
            return [
                'error' => false,
                'message'=> null,
            ];
        }
    }
    public function actionGetusers(){
        $user = Yii::$app->user->identity;
        if($user->admin==0){
            return [
                'error' => TRUE,
                'message' => 'you are not an admin',
            ];
        }
        $users = User::find()->all();
        return [
            'error' => false,
            'message' => null,
            'users' => $users
        ];
    }
}