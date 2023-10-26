<?php

namespace app\controllers;

use app\models\Ban;
use app\models\Price;
use app\models\Type;
use app\models\User;
use app\models\Visit;
use PhpParser\Node\Expr\Print_;
use Yii;
use yii\base\ViewEvent;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;

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
    /*public function actionIndex()
    {
        return $this->render('index');
    }*/
    public function actionRegister(){
        $post = $this->getJsonInput();
        if(User::find()->andWhere(['email'=>$post->email])->one()!=null){
            return [
                'error' => TRUE,
                'message' => "there is user with that email or login",
            ];
        }
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
        if (isset($post->email)) {
            $user->email = $post->email;
        }
        if (isset($post->phone)) {
            $user->phone = $post->phone;
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
        $price = Price::find()->andWhere(['type_id'=>$post->type_id])->one();
        $visit->price = $price->price;
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
        $day = "";
        $post = $this->getJsonInput();
        if(isset($post->date)){
            $day = $post->date;
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
                'status' => 0
            ];
            if ($minutes == 0) $visits[$i]['date'] .= $string;
            $minutes += 30;
        }
        for($i=0;$i<count($visits);$i++) {
            for($j=0;$j<count($visit);$j++) {
                if (str_contains($visits[$i]['date'], $visit[$j]['date'])) {
                    $visits[$i]['status'] = 1;
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
        if(isset($post->phone)){
            $user->phone = $post->phone;
            $user->update();
            return [
                'error' => FALSE,
                'message' => NULL,
            ];
        }
        else {
            return [
                'error' => TRUE,
                'message' => "new phone number is required",
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
}
