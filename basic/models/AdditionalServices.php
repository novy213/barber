<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "additional_services".
 *
 * @property int $id
 * @property string $label
 * @property int $price
 * @property int $time
 *
 * @property AdditionalType[] $additionalTypes
 * @property VisitAdditional[] $visitAdditionals
 */
class AdditionalServices extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'additional_services';
    }

    public function changeType($label, $time, $price){
        $this->label = $label;
        $this->time = $time;
        $this->price = $price;
        $this->updateAttributes(['label']);
        $this->updateAttributes(['time']);
        $this->updateAttributes(['price']);
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['label', 'price', 'time'], 'required'],
            [['price', 'time'], 'integer'],
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
            'price' => 'Price',
            'time' => 'Time',
        ];
    }

    /**
     * Gets query for [[AdditionalTypes]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAdditionalTypes()
    {
        return $this->hasMany(AdditionalType::class, ['additional_id' => 'id']);
    }

    /**
     * Gets query for [[VisitAdditionals]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getVisitAdditionals()
    {
        return $this->hasMany(VisitAdditional::class, ['additional_id' => 'id']);
    }
}
