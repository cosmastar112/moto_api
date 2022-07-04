<?php

namespace app\modules\api\modules\v1\models;

class MotoAlreadyRentedValidator extends \yii\base\BaseObject
{
    public $username;
    public $moto_id;
    public $date_rent_started;
    public $date_rent_ended;
    public $timezone;
    public $db;

    private $_errors = [];

    //нельзя арендовать мотоцикл, который уже в аренде у ДРУГОГО пользователя (независимо от корректности даты аренды),
    //можно арендовать мотоцикл, который уже в аренде, но ТОЛЬКО У ЭТОГО ЖЕ пользователя (независимо от корректности даты аренды)
    public function validateMotoAlreadyRentedByAnotherUser()
    {
        $models = Rent::find()
            ->where(['AND',
                ['moto_id' => $this->moto_id],
                ['!=', 'username', $this->username],
            ])
            ->all($this->db);

        if (!empty($models)) {
            $this->setError('username', 'Мотоцикл арендован другим пользователем');
        }

        if (empty($this->getErrors())) {
            //нет ошибок валидации
            return true;
        }
        //есть ошибки валидации
        return false;
    }

    public function validate()
    {
        //TODO: учитывать время аренды
        $models = Rent::find()
            ->where(['moto_id' => $this->moto_id])
            ->all($this->db);

        if (!empty($models)) {
            $this->setError('moto_id', 'Мотоцикл занят в указанный период');
        }

        if (empty($this->getErrors())) {
            //нет ошибок валидации
            return true;
        }
        //есть ошибки валидации
        return false;
    }

    public function getErrors()
    {
        return $this->_errors;
    }

    public function setError($attribute, $error)
    {
        $this->_errors[$attribute] = $error;
    }
}
