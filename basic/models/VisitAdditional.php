<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "visit_additional".
 *
 * @property int $id
 * @property int $visit_id
 * @property int $additional_id
 *
 * @property AdditionalServices $additional
 * @property Visit $visit
 */
class VisitAdditional extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'visit_additional';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['visit_id', 'additional_id'], 'required'],
            [['visit_id', 'additional_id'], 'integer'],
            [['visit_id'], 'exist', 'skipOnError' => true, 'targetClass' => Visit::class, 'targetAttribute' => ['visit_id' => 'id']],
            [['additional_id'], 'exist', 'skipOnError' => true, 'targetClass' => AdditionalServices::class, 'targetAttribute' => ['additional_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'visit_id' => 'Visit ID',
            'additional_id' => 'Additional ID',
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
     * Gets query for [[Visit]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getVisit()
    {
        return $this->hasOne(Visit::class, ['id' => 'visit_id']);
    }
}
