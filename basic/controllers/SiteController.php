<?php

namespace app\controllers;

use app\models\AdditionalServices;
use app\models\AdditionalType;
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
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;
use function PHPUnit\Framework\stringContains;

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
                'message_user' => $user->getErrorSummary(false),
            ];
        }
    }
    public function actionAddvisit(){
        $post = $this->getJsonInput();
        $user = Yii::$app->user->identity;
        $visit = Visit::find()->andWhere(['date'=>$post->date])->one();
        if(isset($visit)){
            return [
                'error' => true,
                'message_user' => 'ta godzina jest zajeta',
            ];
        }
        $visit = new Visit();
        if(isset($post->date)){
            $visit->date = $post->date;
        }
        if(isset($post->barber_id)){
            $visit->barber_id = $post->barber_id;
        }
        if(isset($post->type_id)){
            $visit->type_id = $post->type_id;
        }
        if(isset($post->additional_info)){
            $visit->additional_info = $post->additional_info;
        }
        $type = Type::find()->andWhere(['id'=>$post->type_id])->one();
        $visit->price = $type->price;
        if(isset($post->razor) && $post->razor==1){
            $type = Type::find()->andWhere(['type'=>'razor'])->one();
            $visit->price=$visit->price+$type->price;
            $visit->razor = 1;
        }
        if(isset($post->coloring) && $post->coloring==1){
            $type = Type::find()->andWhere(['type'=>'coloring'])->one();
            $visit->price=$visit->price+$type->price;
            $visit->coloring = 1;
        }
        $time = Type::find()->andWhere(['id'=>$post->type_id])->one();
        $visit->time = $time->time;
        $visit->user_id = $user->id;
        if($visit->validate()){
            $visit->save();
            return [
                'error' => FALSE,
                'message' => NULL,
            ];
        } else {
            return [
                'error' => true,
                'message' => $visit->getErrorSummary(false),
            ];
        }
    }
    public function actionGetvisits($barber_id){
        $post = $this->getJsonInput();
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
        $visits = array();
        $minutes =0;
        $hours=9;
        for($i=0;$i<19;$i++) {
            $string = "0";
            if ($minutes == 60) {
                $hours++;
                $minutes = 0;
            }
            $visits[] = [
                'date' => $day . " " . $hours . ":" . $minutes,
                'status' => 0,
                'name' => null,
                'last_name' => null,
                'phone' => null,
                'date_end' => null
            ];
            if ($minutes == 0) $visits[$i]['date'] .= $string;
            $minutes += 30;
        }
        for($i=0;$i<count($visits);$i++) {
            for($j=0;$j<count($visit);$j++) {
                if (str_contains($visits[$i]['date'], $visit[$j]['date'])) {
                    $visits[$i]['status'] = 1;
                    $user = $visit[$j]->user;
                    $visits[$i]['name'] = $user->name;
                    $visits[$i]['last_name'] = $user->last_name;
                    $visits[$i]['phone'] = $user->phone;
                    $type = Type::find()->andWhere(['id'=>$visit[$j]->type_id])->one();
                    $dateTime = new \DateTime($visit[$j]->date);
                    $dateTime->modify('+30 minutes');
                    $visits[$i]['date_end'] = $dateTime->format('d-m-Y H:i');
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
        $visit = Visit::find()->andWhere(['user_id'=>$user->id])->all();
        return [
            'error' => FALSE,
            'message' => NULL,
            'visit' => $visit
        ];
    }
    public function actionChangephone(){
        $post = $this->getJsonInput();
        $user = Yii::$app->user->identity;
        if(isset($post->phone) && isset($post->name) && isset($post->last_name)){
            $user->changeData($post->name, $post->last_name, $post->phone);
            return [
                'error' => FALSE,
                'message' => NULL,
            ];
        }
        else {
            return [
                'error' => TRUE,
                'message' => "new phone number or name or last name is required",
            ];
        }
    }
    public function actionDeletevisit(){
        $user = Yii::$app->user->identity;
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
        $visit->delete();
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
        $banUser = new Ban();
        $banUser->user_id = $ban->id;
        $banUser->save();
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
        $ban = Ban::find()->andWhere(['user_id'=>$banUser->id])->one();
        if(is_null($ban)){
            return [
                'error' => TRUE,
                'message' => 'This user is not banned',
            ];
        }
        $ban->delete();
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
        $post = $this->getJsonInput();
        if(!isset($post->date)){
            return[
                'error'=>TRUE,
                'message'=>'Date is required'
            ];
        }
        $visits = Visit::find()->all();
        $date = $post->date;
        for($i=0;$i<count($visits);$i++){
            if(str_contains($visits[$i]->date, $date)) {
                $user = $visits[$i]->user;
                //kod na wyslanie smsa
                $visits[$i]->delete();
            }
        }
        $allDay = false;
        if(strlen($date)<11){
            $allDay=true;
        }
        $visits = array();
        $minutes =0;
        $hours=9;
        if($allDay) {
            for ($i = 0; $i < 19; $i++) {
                $string = "0";
                if ($minutes == 60) {
                    $hours++;
                    $minutes = 0;
                }
                $visits[] = $date . " " . $hours . ":" . $minutes;
                if ($minutes == 0) $visits[$i] .= $string;
                $minutes += 30;
            }

            for ($i = 0; $i < 19; $i++) {
                $visit = new Visit();
                $visit->date = $visits[$i];
                $visit->barber_id = $user->id;
                $visit->user_id = $user->id;
                $visit->price = 0;
                $visit->type_id = 4;
                $visit->time = 30;
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
            $visit = new Visit();
            $visit->date = $date;
            $visit->barber_id = $user->id;
            $visit->user_id = $user->id;
            $visit->price = 0;
            $visit->type_id = 4;
            $visit->time = 30;
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
            'message' => NULL,
        ];
    }
    public function actionUserdata(){
        $user = Yii::$app->user->identity;
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
        $ban = Ban::find()->all();
        $users = array();
        for($i=0;$i<count($ban);$i++){
            $users[] = $ban[$i]->user;
        }
        return [
            'error' => FALSE,
            'message' => NULL,
            'users' => $users
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
    public function actionGetprices(){
        $types = Type::find()->andWhere(['<>', 'id', 8])->all();
        $additional = AdditionalServices::find()->all();
        return [
            'error' => FALSE,
            'message' => NULL,
            'types'=>$types,
            'additional' => $additional
        ];
    }
    public function actionChangeprices(){
        $user = Yii::$app->user->identity;
        if($user->admin==0){
            return [
                'error' => TRUE,
                'message' => 'you are not an admin',
            ];
        }
        $post = $this->getJsonInput();
        for($i=0;$i<count($post->types);$i++){
            $type = Type::find()->andWhere(['id'=>$post->types[$i]->id])->one();
            $type->price = $post->types[$i]->price;
            $type->save();
        }
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
        $type = new Type();
        $type->type = $post->type;
        $type->price = $post->price;
        $type->time = $post->time;
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
                'message' => 'try again later',
            ];
        }
        $user->verified = 1;
        $user->save();
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
        $user->password = $post->password;
        $user->save();
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
        $type->delete();
        return [
            'error' => FALSE,
            'message' => NULL,
        ];
    }
}