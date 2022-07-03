<?php

namespace tests\unit\modules\api\modules\v1\models;

//use tests\unit\modules\api\modules\v1\models\Rent;

class RentTest extends \Codeception\Test\Unit
{
    private function getDb()
    {
        $dbModule = $this->getModule('Db');

        return $dbModule->driver->getDbh();
    }

    protected function setUp(): void
    {
        $pdo = $this->getDb();
        $pdo->query('DELETE FROM rent; ALTER TABLE ad AUTO_INCREMENT = 1;');
        //в БД время в GMT
        $pdo->query("INSERT INTO `rent` (`moto_id`, `username`, `date_rent_started`, `date_rent_ended`, `created_at`) VALUES (1, 'username', '2022-03-07 11:33', '2022-03-08 11:33', '2022-03-08 11:33');");
    }

    protected function tearDown(): void
    {
        $pdo = $this->getDb();
        $pdo->query('DELETE FROM rent; ALTER TABLE ad AUTO_INCREMENT = 1;');
    }

    public function testMotoBusyInPeriod()
    {
        $rent = new Rent();
        $db = Rent::getDb();

        $rent->moto_id = '1';
        $rent->username = 'username';
        $rent->date_rent_started = '2022-03-07 11:33';
        $rent->date_rent_ended = '2022-03-08 11:33';
        $rent->created_at = '2022-03-08 11:33';
        $rent->timezone = 1;

        $validated = $rent->validate();
        $errors = $rent->getErrors();

        $this->assertFalse($validated);
        $this->arrayHasKey('moto_id', $errors);
    }
}
