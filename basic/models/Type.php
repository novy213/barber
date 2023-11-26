<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "type".
 *
 * @property int $id
 * @property string $label
 * @property int $time
 * @property int $price
 *
 * @property AdditionalType[] $additionalTypes
 * @property Visit[] $visits
 */
class Type extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'type';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['label', 'time', 'price'], 'required'],
            [['time', 'price'], 'integer'],
            [['label'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'label' => 'Label',
            'time' => 'Time',
            'price' => 'Price',
        ];
    }

    /**
     * Gets query for [[AdditionalTypes]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAdditionalTypes()
    {
        return $this->hasMany(AdditionalType::class, ['type_id' => 'id']);
    }

    /**
     * Gets query for [[Visits]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getVisits()
    {
        return $this->hasMany(Visit::class, ['type_id' => 'id']);
    }
}
