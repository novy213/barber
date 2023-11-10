<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "additional_services".
 *
 * @property int $id
 * @property string $type
 * @property int $price
 *
 * @property AdditionalType[] $additionalTypes
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

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['type', 'price'], 'required'],
            [['price'], 'integer'],
            [['type'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'type' => 'Type',
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
        return $this->hasMany(AdditionalType::class, ['additional_id' => 'id']);
    }
}
