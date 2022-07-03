<?php

namespace app\modules\api\modules\v1\models;

class MotoAlreadyRentedValidator extends \yii\base\BaseObject
{
    public $moto_id;
    public $date_rent_started;
    public $date_rent_ended;
    public $timezone;
    public $db;

    private $_errors = [];

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
