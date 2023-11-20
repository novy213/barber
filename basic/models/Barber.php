<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "barber".
 *
 * @property int $id
 * @property string $name
 * @property string $last_name
 * @property int $user_id
 * @property string $hour_start
 * @property string $hour_end
 *
 * @property User $user
 * @property Visit[] $visits
 */
class Barber extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'barber';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'last_name', 'user_id', 'hour_start', 'hour_end'], 'required'],
            [['user_id'], 'integer'],
            [['name', 'last_name', 'hour_start', 'hour_end'], 'string', 'max' => 255],
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
            'name' => 'Name',
            'last_name' => 'Last Name',
            'user_id' => 'User ID',
            'hour_start' => 'Hour Start',
            'hour_end' => 'Hour End',
        ];
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

    /**
     * Gets query for [[Visits]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getVisits()
    {
        return $this->hasMany(Visit::class, ['barber_id' => 'id']);
    }
}
