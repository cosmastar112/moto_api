<?php

namespace app\models;

class GMTDatetime extends \yii\base\Model
{
    /** @var string  Дата в формате 'Y-m-d H:i:s' */
    public $datetime;

    /**
     * @var string Смещение от GMT, например: 4 (GMT+4) или -4 (GMT-4).
     * Диапазон доступных значений: [-12; 12].
     */
    public $gmtOffset;

    public function rules()
    {
        return [
            [['datetime', 'gmtOffset'], 'required'],
            [['datetime'], 'string'],
            [['gmtOffset'], 'number', 'max' => 12, 'min' => -12],
        ];
    }

    public function normalize()
    {
        if (!$this->validate()) {
            $errors = $this->getErrors();
            return false;
        }

        /** @var string $gmtOffsetString Смещение от GMT */
        $gmtOffsetString = $this->gmtOffset > 0 ? "+$this->gmtOffset" : "$this->gmtOffset";
        /** @var DateTime $date Дата со смещением от GMT */
        $date = \DateTime::createFromFormat('Y-m-d H:i:s', $this->datetime, new \DateTimeZone("GMT$gmtOffsetString"));

        $gmtDate = clone $date;
        /** @var DateTime $gmtDate Дата в GMT+0 */
        $gmtDate->setTimezone(new \DateTimeZone('GMT'));

        return $gmtDate->format('Y-m-d H:i:s');
    }
}
