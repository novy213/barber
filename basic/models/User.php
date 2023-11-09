<?php

namespace app\models;

use yii\db\ActiveRecord;

/**
 * This is the model class for table "user".
 *
 * @property int $id
 * @property string $password
 * @property string $name
 * @property string $last_name
 * @property string $email
 * @property int $phone
 * @property int|null $admin
 * @property string|null $access_token
 *
 * @property Visit[] $visits
 */

class User extends ActiveRecord implements \yii\web\IdentityInterface
{
    public static function tableName()
    {
        return 'user';
    }
    public function rules()
    {
        return [
            [['password', 'name', 'last_name', 'email', 'phone'], 'required'],
            [['phone', 'admin'], 'integer'],
            [['password', 'name', 'last_name', 'email', 'access_token'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'password' => 'Password',
            'name' => 'Name',
            'last_name' => 'Last Name',
            'email' => 'Email',
            'phone' => 'Phone',
            'admin' => 'Admin',
            'access_token' => 'Access Token',
        ];
    }
    public function createApiToken()
    {
        $this->access_token = \Yii::$app->security->generateRandomString();
        $this->updateAttributes(['access_token']);
        return $this->access_token;
    }
    public function clearApiToken()
    {
        $this->access_token = null;
        $this->updateAttributes(['access_token']);
    }
    /**
     * {@inheritdoc}
     */
    public static function findIdentity($id)
    {
        return self::findOne(['id' => $id]);
    }

    /**
     * {@inheritdoc}
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        $user = self::findOne(['access_token' => $token]);
        return $user;
    }

    public static function findByPhone($phone)
    {
        return self::findOne(['phone' => $phone]);
    }
    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->id;
    }
    public function getAuthKey()
    {

    }

    /**
     * {@inheritdoc}
     */
    public function validateAuthKey($authKey)
    {

    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return bool if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return password_verify($password, $this->password);
    }

    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            $this->password = password_hash($this->password, PASSWORD_BCRYPT);
            return true;
        }
        return false;
    }
    public function getVisits()
    {
        return $this->hasMany(Visit::class, ['user_id' => 'id']);
    }
}
