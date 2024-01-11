<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "message".
 *
 * @property int $id
 * @property string $message
 * @property int $barber_id
 * @property int $user_id
 * @property string $date
 *
 * @property Barber $barber
 * @property User $user
 */
class Message extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'message';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['message', 'barber_id', 'user_id', 'date'], 'required'],
            [['barber_id', 'user_id'], 'integer'],
            [['message', 'date'], 'string', 'max' => 255],
            [['barber_id'], 'exist', 'skipOnError' => true, 'targetClass' => Barber::class, 'targetAttribute' => ['barber_id' => 'id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'message' => 'Message',
            'barber_id' => 'Barber ID',
            'user_id' => 'User ID',
            'date' => 'Date',
        ];
    }
    public function readBarber(){
        $this->barber_readed = 1;
        $this->updateAttributes(['barber_readed']);
    }
    public function readUser(){
        $this->user_readed = 1;
        $this->updateAttributes(['user_readed']);
    }
    /**
     * Gets query for [[Barber]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getBarber()
    {
        return $this->hasOne(Barber::class, ['id' => 'barber_id']);
    }

    /**
     * Gets query for [[User]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }
}
