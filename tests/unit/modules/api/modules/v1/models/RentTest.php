<?php

namespace tests\unit\modules\api\modules\v1\models;

class RentTest extends \Codeception\Test\Unit
{
    //некорректно указана таймзона
    public function testIncorrectTimezone()
    {
        $rent = new Rent();

        $rent->moto_id = 1;
        $rent->username = 'username1';
        $rent->date_rent_started = '2022-08-21 14:36:00';
        $rent->date_rent_ended = '2022-08-22 14:36:00';
        $rent->timezone = 'IncorrectTimezone';

        $validated = $rent->validate();
        $errors = $rent->getErrors();

        $this->assertFalse($validated);
        $this->assertArrayHasKey('timezone', $errors);
    }

    //некорректно указано время начала периода
    public function testIncorrectStartedDatetimeFormat()
    {
        $rent = new Rent();

        $rent->moto_id = 1;
        $rent->username = 'username1';
        $rent->date_rent_started = '21-08-2022 14:36:00'; //некорректный формат времени
        $rent->date_rent_ended = '2022-08-22 14:36:00';
        $rent->timezone = 'Europe/Samara';

        $validated = $rent->validate();
        $errors = $rent->getErrors();

        $this->assertFalse($validated);
        $this->assertArrayHasKey('date_rent_started', $errors);
    }

    //некорректно указано время конца периода
    public function testIncorrectEndedDatetimeFormat()
    {
        $rent = new Rent();

        $rent->moto_id = 1;
        $rent->username = 'username1';
        $rent->date_rent_started = '2022-08-21 14:36:00';
        $rent->date_rent_ended = '22-08-2022 14:36:00'; //некорректный формат времени
        $rent->timezone = 'Europe/Samara';

        $validated = $rent->validate();
        $errors = $rent->getErrors();

        $this->assertFalse($validated);
        $this->assertArrayHasKey('date_rent_ended', $errors);
    }

    //чтобы валидатор занятости мотоцикла работал, даты периода аренды должны быть указаны корректно
    public function testAlreadyRentedValidatorNotRunningIfPeriodDatesIncorrect()
    {
        $rent = new Rent();

        $rent->moto_id = 1;
        $rent->username = 'username1';
        $rent->date_rent_started = '1';
        $rent->date_rent_ended = '1';
        $rent->timezone = '1';

        $validated = $rent->validate();
        $errors = $rent->getErrors();

        $this->assertFalse($validated);
        $this->assertArrayHasKey('date_rent_started', $errors);
        $this->assertArrayHasKey('date_rent_ended', $errors);
        //валидатор не сработал, т.к. даты периода указаны некорректно
        $this->assertArrayHasKey('moto_id', $errors);
    }
}
