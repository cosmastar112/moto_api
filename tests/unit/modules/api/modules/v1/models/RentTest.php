<?php

namespace tests\unit\modules\api\modules\v1\models;

class RentTest extends \Codeception\Test\Unit
{
    //чтобы валидатор занятости мотоцикла рабатал, даты периода аренды должны быть указаны корректно
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
