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
}
