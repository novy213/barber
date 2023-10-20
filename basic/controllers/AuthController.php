<?php

namespace app\controllers;

use app\models\Ban;
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
        $email = isset($post->email) ? $post->email : NULL;
        $password = isset($post->password) ? $post->password : NULL;
        $phone = isset($post->phone) ? $post->phone : NULL;
        if(is_null($email) && is_null($phone)){
            Yii::$app->response->statusCode = 400;
            return ['error' => TRUE, 'message' => 'Phone or Email are required.'];
        }
        if (is_null($password)) {
            Yii::$app->response->statusCode = 400;
            return ['error' => TRUE, 'message' => 'Password is required.'];
        }
        if(is_null($phone)) $user = User::findByEmail($email);
        else $user = User::findByPhone($phone);
        if (is_null($user)) {
            Yii::$app->response->statusCode = 400;
            return ['error' => TRUE, 'message' => 'Incorrect email or phone or password.'];
        }
        if (!$user->validatePassword($password)) {
            Yii::$app->response->statusCode = 400;
            return ['error' => TRUE, 'message' => 'Incorrect email or phone or password.'];
        }
        $ban = Ban::find()->andWhere(['user_id'=>$user->id])->one();
        if(isset($ban)) {
            return [
                'error' => TRUE,
                'message' => 'you are banned'
            ];
        }
        $token = $user->createApiToken();
        return [
            'error' => FALSE,
            'message' => NULL,
            'token' => $token,
            'userId' => $user->id,
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
        return [
            'error' => FALSE,
            'message' => NULL,
        ];
    }
}