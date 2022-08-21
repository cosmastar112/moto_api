<?php

namespace app\modules\api\modules\v1\models;

use yii\behaviors\TimestampBehavior;
use app\modules\api\modules\v1\Module;

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
     * @var string Таймзона, например: Europe/Samara.
     * @see https://www.php.net/manual/ru/timezones.php
     */
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
            ['moto_id', 'integer'],
            ['timezone', 'validateTimezone'],
            [['created_at', 'updated_at'], 'safe'],
            [['date_rent_started', 'date_rent_ended', 'timezone'], 'string'],
            [['date_rent_started', 'date_rent_ended'], 'date', 'format' => 'php:Y-m-d H:i:s', 'message' => Module::t('errors', 'The format of {attribute} is invalid')],
            [['date_rent_started', 'date_rent_ended'], 'filter', 'filter' => function($value) {
                $model = new UTCDatetime();
                $model->datetime = $value;
                $model->timezone = $this->timezone;
                $result = $model->normalize();

                return $result;
            }],
            [['username'], 'string', 'max' => 64],
            ['moto_id', 'validateMotoAlreadyRentedByAnotherUser'],
            ['moto_id', 'validateMotoAlreadyRentedByPeriod'],
        ];
    }

    public function validateTimezone($attribute, $params)
    {
        if (!in_array($this->$attribute, \DateTimeZone::listIdentifiers())) {
            $this->addError($attribute, Module::t('errors', 'Unknown or bad timezone'));
        }
    }

    //нельзя арендовать мотоцикл, который уже в аренде у ДРУГОГО пользователя (независимо от корректности даты аренды)
    public function validateMotoAlreadyRentedByAnotherUser($attribute, $params)
    {
        if ($this->hasErrors('date_rent_started') || $this->hasErrors('date_rent_ended')) {
            $this->addError($attribute, Module::t('errors', 'Period is incorrect, can\'t check is motorbike already rented by another user or not'));
        } else {
            //валидация выполняется только если даты указаного периода (начало и конец аренды) корректные: являются строками в правильном формате даты-времени
            $validator = new MotoAlreadyRentedValidator([
                'username' => $this->username,
                'moto_id' => $this->moto_id,
                'db' => self::getDb(),
            ]);
            if (!$validator->validateMotoAlreadyRentedByAnotherUser()) {
                foreach ($validator->getErrors() as $errorKey => $errorMessage) {
                    $this->addError($errorKey, $errorMessage);
                }
            }
        }
    }

    public function validateMotoAlreadyRentedByPeriod($attribute, $params)
    {
        if ($this->hasErrors('date_rent_started') || $this->hasErrors('date_rent_ended')) {
            $this->addError($attribute, Module::t('errors', 'Period is incorrect, can\'t check is motorbike already rented at this period or not'));
        } else {
            //валидация выполняется только если даты указаного периода (начало и конец аренды) корректные: являются строками в правильном формате даты-времени
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
