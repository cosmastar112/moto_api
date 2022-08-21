<?php

namespace app\modules\api\modules\v1\models;

class UTCDatetime extends \yii\base\Model
{
    /** @var string  Дата в формате 'Y-m-d H:i:s' */
    public $datetime;

    /**
     * @var string Таймзона, например: Europe/Samara.
     * @see https://www.php.net/manual/ru/timezones.php
     */
    public $timezone;

    public function rules()
    {
        return [
            [['datetime', 'timezone'], 'required'],
            [['datetime', 'timezone'], 'string'],
            ['datetime', 'date', 'format' => 'php:Y-m-d H:i:s', 'message' => 'The format of {attribute} is invalid.'],
            ['timezone', 'validateTimezone'],
        ];
    }

    public function validateTimezone($attribute, $params)
    {
        if (!in_array($this->$attribute, \DateTimeZone::listIdentifiers())) {
            $this->addError($attribute, 'Unknown or bad timezone');
        }
    }

    public function normalize()
    {
        if (!$this->validate()) {
            $errors = $this->getErrors();
            return false;
        }

        /** @var DateTimeImmutable $date Дата в указанном часовом поясе. */
        $date = \DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $this->datetime, new \DateTimeZone($this->timezone));
        /** @var DateTimeImmutable $utcDate Дата в UTC */
        $utcDate = $date->setTimezone(new \DateTimeZone('UTC'));

        return $utcDate->format('Y-m-d H:i:s');
    }
}
