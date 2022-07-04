<?php

namespace tests\unit\modules\api\modules\v1\models;

use app\modules\api\modules\v1\models\MotoAlreadyRentedValidator;

class MotoAlreadyRentedValidatorTest extends \Codeception\Test\Unit
{
    protected function setUp(): void
    {
        $pdo = $this->getDb();
        $pdo->query('DELETE FROM rent; ALTER TABLE ad AUTO_INCREMENT = 1;');
    }

    protected function tearDown(): void
    {
        $pdo = $this->getDb();
        $pdo->query('DELETE FROM rent; ALTER TABLE ad AUTO_INCREMENT = 1;');
    }

    private function getDb()
    {
        $dbModule = $this->getModule('Db');

        return $dbModule->driver->getDbh();
    }

    protected function _setUpBeforeAlreadyRentedByAnotherUser($username, $moto_id): void
    {
        $pdo = $this->getDb();
        $sth = $pdo->prepare("INSERT INTO `rent` (`moto_id`, `username`, `date_rent_started`, `date_rent_ended`, `created_at`) VALUES (:moto_id, :username, '2022-03-07 11:33', '2022-03-08 11:33', '2022-03-08 11:33');");
        $sth->bindParam(':username', $username, \PDO::PARAM_STR);
        $sth->bindParam(':moto_id', $moto_id, \PDO::PARAM_INT);
        $sth->execute();
    }

    protected function _setUpBeforeAlreadyRentedByPeriod($date_rent_started, $date_rent_ended): void
    {
        $pdo = $this->getDb();
        $sth = $pdo->prepare("INSERT INTO `rent` (`moto_id`, `username`, `date_rent_started`, `date_rent_ended`, `created_at`) VALUES (1, 2, :date_rent_started, :date_rent_ended, '2022-03-08 11:33');");
        $sth->bindParam(':date_rent_started', $date_rent_started, \PDO::PARAM_STR);
        $sth->bindParam(':date_rent_ended', $date_rent_ended, \PDO::PARAM_STR);
        $sth->execute();
    }

    //нельзя арендовать мотоцикл, который уже в аренде у ДРУГОГО пользователя (независимо от корректности даты аренды)
    public function testAlreadyRentedByAnotherUser()
    {
        $this->_setUpBeforeAlreadyRentedByAnotherUser('username10', 1);

        $validator = new MotoAlreadyRentedValidator([
            'username' => 'username2',
            'moto_id' => 1,
            'db' => Rent::getDb(),
        ]);
        $validated = $validator->validateMotoAlreadyRentedByAnotherUser();
        $errors = $validator->getErrors();

        $this->assertFalse($validated);
        $this->assertArrayHasKey('username', $errors);
    }

    //можно арендовать мотоцикл, который уже в аренде, но ТОЛЬКО У ЭТОГО ЖЕ пользователя (независимо от корректности даты аренды)
    public function testAlreadyRentedByThatUser()
    {
        $username = 'username10';
        $moto_id = 1;
        $this->_setUpBeforeAlreadyRentedByAnotherUser($username, $moto_id);

        $validator = new MotoAlreadyRentedValidator([
            'username' => $username,
            'moto_id' => $moto_id,
            'db' => Rent::getDb(),
        ]);
        $validated = $validator->validateMotoAlreadyRentedByAnotherUser();

        $this->assertTrue($validated);
    }

    //нельзя арендовать мотоцикл, который уже в аренде (по времени аренды)
    public function testAlreadyRentedByPeriod1()
    {
        $this->_setUpBeforeAlreadyRentedByPeriod('2022-03-07 11:33:00', '2022-03-08 11:33:00');

        $validator = new MotoAlreadyRentedValidator([
            'moto_id' => 1,
            'date_rent_started' => '2022-03-07 06:10:00',
            'date_rent_ended' => '2022-03-07 12:33:00', // <-- правая граница входит в занятый период
            'db' => Rent::getDb(),
        ]);
        $validated = $validator->validateMotoAlreadyRentedByPeriod();
        $errors = $validator->getErrors();

        $this->assertFalse($validated);
        $this->assertArrayHasKey('moto_id', $errors);
    }

    //нельзя арендовать мотоцикл, который уже в аренде (по времени аренды)
    public function testAlreadyRentedByPeriod2()
    {
        $this->_setUpBeforeAlreadyRentedByPeriod('2022-03-07 11:33:00', '2022-03-08 11:33:00');

        $validator = new MotoAlreadyRentedValidator([
            'moto_id' => 1,
            'date_rent_started' => '2022-03-07 15:00:00', // <-- левая граница входит в занятый период
            'date_rent_ended' => '2022-03-08 13:33:00',
            'db' => Rent::getDb(),
        ]);
        $validated = $validator->validateMotoAlreadyRentedByPeriod();
        $errors = $validator->getErrors();

        $this->assertFalse($validated);
        $this->assertArrayHasKey('moto_id', $errors);
    }

    //нельзя арендовать мотоцикл, который уже в аренде (по времени аренды)
    public function testAlreadyRentedByPeriod3()
    {
        $this->_setUpBeforeAlreadyRentedByPeriod('2022-03-07 11:33:00', '2022-03-08 11:33:00');

        //занятый период находится между левой и правой границами
        $validator = new MotoAlreadyRentedValidator([
            'moto_id' => 1,
            'date_rent_started' => '2022-03-07 06:10:00',   // <-- левая граница не входит в занятый период
            'date_rent_ended' => '2022-03-08 13:33:00',     // <-- правая граница не входит в занятый период
            'db' => Rent::getDb(),
        ]);
        $validated = $validator->validateMotoAlreadyRentedByPeriod();
        $errors = $validator->getErrors();

        $this->assertFalse($validated);
        $this->assertArrayHasKey('moto_id', $errors);
    }

    //нельзя арендовать мотоцикл, который уже в аренде (по времени аренды)
    public function testAlreadyRentedByPeriod4()
    {
        $this->_setUpBeforeAlreadyRentedByPeriod('2022-03-07 11:33:00', '2022-03-08 11:33:00');

        //левая и правая границы входят в занятый период
        $validator = new MotoAlreadyRentedValidator([
            'moto_id' => 1,
            'date_rent_started' => '2022-03-07 12:10:00',  // <-- левая граница входит в занятый период
            'date_rent_ended' => '2022-03-07 13:33:00',    // <-- правая граница входит в занятый период
            'db' => Rent::getDb(),
        ]);
        $validated = $validator->validateMotoAlreadyRentedByPeriod();
        $errors = $validator->getErrors();

        $this->assertFalse($validated);
        $this->assertArrayHasKey('moto_id', $errors);
    }

    //можно арендовать мотоцикл, который не аренде в указанный период (по времени аренды)
    public function testAlreadyRentedByPeriod5()
    {
        $this->_setUpBeforeAlreadyRentedByPeriod('2022-03-07 11:33:00', '2022-03-08 11:33:00');

        //левая и правая границы не входят в занятый период
        $validator = new MotoAlreadyRentedValidator([
            'moto_id' => 1,
            'date_rent_started' => '2022-03-06 06:33:00',  // <-- левая граница не входит в занятый период
            'date_rent_ended' => '2022-03-07 06:33:00',    // <-- правая граница не входит в занятый период
            'db' => Rent::getDb(),
        ]);
        $validated = $validator->validateMotoAlreadyRentedByPeriod();

        $this->assertTrue($validated);
    }

    //можно арендовать мотоцикл, который не аренде в указанный период (по времени аренды)
    public function testAlreadyRentedByPeriod6()
    {
        $this->_setUpBeforeAlreadyRentedByPeriod('2022-03-07 11:33:00', '2022-03-08 11:33:00');

        //левая и правая границы не входят в занятый период
        $validator = new MotoAlreadyRentedValidator([
            'moto_id' => 1,
            'date_rent_started' => '2022-03-08 13:33:00',  // <-- левая граница не входит в занятый период
            'date_rent_ended' => '2022-03-09 13:33:00',    // <-- правая граница не входит в занятый период
            'db' => Rent::getDb(),
        ]);
        $validated = $validator->validateMotoAlreadyRentedByPeriod();

        $this->assertTrue($validated);
    }
}
