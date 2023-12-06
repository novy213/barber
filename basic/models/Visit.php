<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "visit".
 *
 * @property int $id
 * @property string $date
 * @property int $barber_id
 * @property float $price
 * @property int $type_id
 * @property boolean $notified
 * @property float $time
 * @property string|null $additional_info
 * @property int $user_id
 *
 * @property Barber $barber
 * @property Type $type
 * @property User $user
 */
class Visit extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'visit';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['date', 'barber_id', 'price', 'type_id', 'time', 'user_id'], 'required'],
            [['barber_id', 'type_id', 'user_id'], 'integer'],
            [['price', 'time'], 'number'],
            [['date', 'additional_info'], 'string', 'max' => 255],
            [['barber_id'], 'exist', 'skipOnError' => true, 'targetClass' => Barber::class, 'targetAttribute' => ['barber_id' => 'id']],
            [['type_id'], 'exist', 'skipOnError' => true, 'targetClass' => Type::class, 'targetAttribute' => ['type_id' => 'id']],
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
            'date' => 'Date',
            'barber_id' => 'Barber ID',
            'price' => 'Price',
            'type_id' => 'Type ID',
            'time' => 'Time',
            'additional_info' => 'Additional Info',
            'user_id' => 'User ID',
        ];
    }
    public function updateVisit($additional_info){
        $this->additional_info = $additional_info;
        $this->updateAttributes(['additional_info']);
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
     * Gets query for [[Type]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getType()
    {
        return $this->hasOne(Type::class, ['id' => 'type_id']);
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
