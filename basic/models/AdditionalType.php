<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "additional_type".
 *
 * @property int $id
 * @property int $additional_id
 * @property int $type_id
 *
 * @property AdditionalServices $additional
 * @property Type $type
 */
class AdditionalType extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'additional_type';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['additional_id', 'type_id'], 'required'],
            [['additional_id', 'type_id'], 'integer'],
            [['additional_id'], 'exist', 'skipOnError' => true, 'targetClass' => AdditionalServices::class, 'targetAttribute' => ['additional_id' => 'id']],
            [['type_id'], 'exist', 'skipOnError' => true, 'targetClass' => Type::class, 'targetAttribute' => ['type_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'additional_id' => 'Additional ID',
            'type_id' => 'Type ID',
        ];
    }

    /**
     * Gets query for [[Additional]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAdditional()
    {
        return $this->hasOne(AdditionalServices::class, ['id' => 'additional_id']);
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
}
