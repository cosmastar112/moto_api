<?php

namespace app\modules\api\modules\v1\models;

use yii\behaviors\TimestampBehavior;

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
    public $timezone;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'rent';
    }

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors[] = [
            'class' => TimestampBehavior::class,
            'value' => gmdate('Y-m-d H:i:s'),
        ];

        return $behaviors;
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['username', 'moto_id', 'date_rent_started', 'date_rent_ended', 'timezone'], 'required'],
            [['moto_id', 'timezone'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['date_rent_started', 'date_rent_ended'], 'string'],
            [['username'], 'string', 'max' => 64],
            ['moto_id', 'validateMotoAlreadyRented']
        ];
    }

    public function validateMotoAlreadyRented($attribute, $params)
    {
        $validator = new MotoAlreadyRentedValidator([
            'moto_id' => $this->moto_id,
            'date_rent_started' => $this->date_rent_started,
            'date_rent_ended' => $this->date_rent_ended,
            'timezone' => $this->timezone,
            'db' => self::getDb(),
        ]);
        if (!$validator->validate()) {
            foreach ($validator->getErrors() as $errorKey => $errorMessage) {
                $this->addError($errorKey, $errorMessage);
            }
        }
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
