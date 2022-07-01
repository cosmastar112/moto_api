<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "rent".
 *
 * @property int $id
 * @property string $username
 * @property int $moto_id
 * @property string $date_rent_started
 * @property string $date_rent_ended
 * @property string $created_at
 * @property string|null $updated_at
 */
class Rent extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'rent';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['username', 'moto_id', 'date_rent_started', 'date_rent_ended', 'created_at'], 'required'],
            [['moto_id'], 'integer'],
            [['date_rent_started', 'date_rent_ended', 'created_at', 'updated_at'], 'safe'],
            [['username'], 'string', 'max' => 64],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'username' => 'Username',
            'moto_id' => 'Moto ID',
            'date_rent_started' => 'Date Rent Started',
            'date_rent_ended' => 'Date Rent Ended',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }
}
