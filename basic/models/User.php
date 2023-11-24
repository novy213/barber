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
 * @property int $notification
 * @property int $phone
 * @property int|null $admin
 * @property int|null $verified
 * @property string|null $access_token
 *
 * @property Ban[] $bans
 * @property Barber[] $barbers
 * @property Code[] $codes
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
            [['password', 'name', 'last_name', 'phone'], 'required'],
            [['phone', 'admin', 'verified', 'notification'], 'integer'],
            [['password', 'name', 'last_name', 'access_token'], 'string', 'max' => 255],
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
            'phone' => 'Phone',
            'admin' => 'Admin',
            'verified' => 'Verified',
            'access_token' => 'Access Token',
            'notification' => 'Notification',
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
        $userPhone = 48;
        $userPhone.=$phone;
        return self::findOne(['phone' => $userPhone]);
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
    public function changeNoti($noti){
        $this->notification = $noti;
        $this->updateAttributes(['notification']);
    }
    public function changeData($name, $last_name, $phone){
        $this->name = $name;
        $this->last_name = $last_name;
        $this->phone = 48;
        $this->phone .= $phone;
        $this->updateAttributes(['name']);
        $this->updateAttributes(['last_name']);
        $this->updateAttributes(['phone']);
    }
    public function verify(){
        $this->verified = 1;
        $this->updateAttributes(['verified']);
    }
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            $this->password = password_hash($this->password, PASSWORD_BCRYPT);
            return true;
        }
        return false;
    }
    public function getBans()
    {
        return $this->hasMany(Ban::class, ['user_id' => 'id']);
    }

    /**
     * Gets query for [[Barbers]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getBarbers()
    {
        return $this->hasMany(Barber::class, ['user_id' => 'id']);
    }

    /**
     * Gets query for [[Codes]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCodes()
    {
        return $this->hasMany(Code::class, ['user_id' => 'id']);
    }

    /**
     * Gets query for [[Visits]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getVisits()
    {
        return $this->hasMany(Visit::class, ['user_id' => 'id']);
    }
}
