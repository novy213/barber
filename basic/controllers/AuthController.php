<?php

namespace app\controllers;

use app\models\Ban;
use app\models\Barber;
use app\models\Teacher;
use Yii;

use app\models\User;

use app\models\ApiToken;

use app\models\Legal;

use app\models\Client;
use app\components\Controller;

class AuthController extends Controller
{
    /**
     * Log in action
     *
     * @return string
     */
    public function actionLogin()
    {
        $post = $this->getJsonInput();
        $password = isset($post->password) ? $post->password : NULL;
        $phone = isset($post->phone) ? $post->phone : NULL;
        if(is_null($phone)){
            Yii::$app->response->statusCode = 400;
            return ['error' => TRUE, 'message' => 'Phone is required.'];
        }
        if (is_null($password)) {
            Yii::$app->response->statusCode = 400;
            return ['error' => TRUE, 'message' => 'Password is required.'];
        }
        $user = User::findByPhone($phone);
        if (is_null($user)) {
            Yii::$app->response->statusCode = 400;
            return ['error' => TRUE, 'message' => 'Incorrect phone or password.'];
        }
        if (!$user->validatePassword($password)) {
            Yii::$app->response->statusCode = 400;
            return ['error' => TRUE, 'message' => 'Incorrect  phone or password.'];
        }
        if($user->ban) {
            return [
                'error' => TRUE,
                'message' => 'you are banned'
            ];
        }
        $token = $user->createApiToken();
        $barber = Barber::find()->andWhere(['user_id'=>$user->id])->one();
        $barber_id = null;
        if(!is_null($barber)) {
            $barber_id = $barber->id;
        }
        if (isset($post->notification_token)) {
            $user->updateNotificationtoken($post->notification_token);
        }
        else {
            return ['error' => TRUE, 'message' => 'notification token is required'];
        }
        return [
            'error' => FALSE,
            'message' => NULL,
            'token' => $token,
            'userId' => $user->id,
            'admin' => $user->admin,
            'verified' => $user->verified,
            'barber_id'=>$barber_id
        ];
    }
    /**
     * Log out action
     *
     * @return string
     */

    public function actionLogout()
    {
        $user = Yii::$app->user->identity;
        $user->clearApiToken();
        $user->updateNotificationtoken(null);
        return [
            'error' => FALSE,
            'message' => NULL,
        ];
    }
}