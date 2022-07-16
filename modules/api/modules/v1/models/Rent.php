<?php

namespace app\modules\api\modules\v1\models;

use yii\behaviors\TimestampBehavior;
use app\models\GMTDatetime;

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
            [['date_rent_started', 'date_rent_ended'], 'date', 'format' => 'php:Y-m-d H:i:s'],
            [['date_rent_started', 'date_rent_ended'], 'filter', 'filter' => function($value) {
                $model = new GMTDatetime();
                $model->datetime = $value;
                $model->gmtOffset = $this->timezone;
                $result = $model->normalize();

                return $result;
            }],
            [['username'], 'string', 'max' => 64],
            ['username', 'validateMotoAlreadyRentedByAnotherUser'],
            ['moto_id', 'validateMotoAlreadyRentedByPeriod'],
        ];
    }

    //нельзя арендовать мотоцикл, который уже в аренде у ДРУГОГО пользователя (независимо от корректности даты аренды)
    public function validateMotoAlreadyRentedByAnotherUser($attribute, $params)
    {
        $validator = new MotoAlreadyRentedValidator([
            'username' => $this->$attribute,
            'moto_id' => $this->moto_id,
            'db' => self::getDb(),
        ]);
        if (!$validator->validateMotoAlreadyRentedByAnotherUser()) {
            foreach ($validator->getErrors() as $errorKey => $errorMessage) {
                $this->addError($errorKey, $errorMessage);
            }
        }
    }

    public function validateMotoAlreadyRentedByPeriod($attribute, $params)
    {
        $validator = new MotoAlreadyRentedValidator([
            'moto_id' => $this->moto_id,
            'date_rent_started' => $this->date_rent_started,
            'date_rent_ended' => $this->date_rent_ended,
            'db' => self::getDb(),
        ]);
        if (!$validator->validateMotoAlreadyRentedByPeriod()) {
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
