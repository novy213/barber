<?php

namespace app\controllers;

use app\models\User;
use Yii;
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

}
