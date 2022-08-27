<?php

namespace tests\unit\modules\api\modules\v1\models;

use app\modules\api\modules\v1\models\MotoAlreadyRentedValidator;
use app\modules\api\modules\v1\models\User;

class MotoAlreadyRentedValidatorTest extends \Codeception\Test\Unit
{
    protected function setUp(): void
    {
        $pdo = $this->getDb();
        $pdo->query('DELETE FROM rent; ALTER TABLE ad AUTO_INCREMENT = 1;');
        $pdo->query('DELETE FROM user; ALTER TABLE user AUTO_INCREMENT = 1;');
    }

    protected function tearDown(): void
    {
        $pdo = $this->getDb();
        $pdo->query('DELETE FROM rent; ALTER TABLE ad AUTO_INCREMENT = 1;');
        $pdo->query('DELETE FROM user; ALTER TABLE user AUTO_INCREMENT = 1;');
    }

    private function getDb()
    {
        $dbModule = $this->getModule('Db');

        return $dbModule->driver->getDbh();
    }

    protected function _setUpBeforeAlreadyRentedByPeriod($date_rent_started, $date_rent_ended): void
    {
        $db = \Yii::$app->test_db;

        //создать пользователя, который уже арендовал мотоцикл
        $username = 'username';
        $db->createCommand()->insert(User::tableName(), [
            'username' => $username,
            'email' => $username . '@mail.ru',
            'fio' => $username . ' Name',
            'created_at' => '2022-03-08 11:33',
        ])->execute();

        /** @var \yii\db\Connection $userId Id созданной записи (id пользователя). */
        $userId = $db->getLastInsertID();

        $db->createCommand()->insert(Rent::tableName(), [
            'moto_id' => 1,
            'user_id' => $userId,
            'date_rent_started' => $date_rent_started,
            'date_rent_ended' => $date_rent_ended,
            'created_at' => '2022-03-08 11:33',
        ])->execute();
    }

    //нельзя арендовать мотоцикл если пользователь не существует
    public function testUserDoesntExists()
    {
        //ТЕСТ
        $validator = new MotoAlreadyRentedValidator([
            'username' => 'username',
            'moto_id' => 1,
            'db' => Rent::getDb(),
        ]);
        $validated = $validator->validateMotoAlreadyRentedByAnotherUser();
        $errors = $validator->getErrors();

        $this->assertFalse($validated);
        $this->assertArrayHasKey('username', $errors);
    }

    //нельзя арендовать мотоцикл, который уже в аренде у ДРУГОГО пользователя (независимо от корректности даты аренды)
    public function testAlreadyRentedByAnotherUser()
    {
        ////////////////////////////
        //начало настройки БД перед запуском теста
        /** @var \yii\db\Connection $db Соединение с тестовой БД. */
        $db = \Yii::$app->test_db;

        //создать пользователя, который уже арендовал мотоцикл
        $username10 = 'username10';
        $db->createCommand()->insert(User::tableName(), [
            'username' => $username10,
            'email' => $username10 . '@mail.ru',
            'fio' => $username10 . ' Name',
            'created_at' => '2022-03-08 11:33',
        ])->execute();

        /** @var \yii\db\Connection $userId Id созданной записи (id пользователя). */
        $userId = $db->getLastInsertID();

        //создать аренду
        $db->createCommand()->insert(Rent::tableName(), [
            'moto_id' => 1,
            'user_id' => $userId,
            'date_rent_started' => '2022-03-07 11:33',
            'date_rent_ended' => '2022-03-08 11:33',
            'created_at' => '2022-03-08 11:33',
        ])->execute();

        //создать пользователя, который пытается арендовать мотоцикл
        $username2 = 'username2';
        $db->createCommand()->insert(User::tableName(), [
            'username' => $username2,
            'email' => $username2 . '@mail.ru',
            'fio' => $username2 . ' Name',
            'created_at' => '2022-03-08 11:33',
        ])->execute();
        //конец настройки БД перед запуском теста
        ////////////////////////////

        //ТЕСТ
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
        ////////////////////////////
        //начало настройки БД перед запуском теста
        /** @var \yii\db\Connection $db Соединение с тестовой БД. */
        $db = \Yii::$app->test_db;

        //создать пользователя, который уже арендовал мотоцикл
        $username = 'username';
        $db->createCommand()->insert(User::tableName(), [
            'username' => $username,
            'email' => $username . '@mail.ru',
            'fio' => $username . ' Name',
            'created_at' => '2022-03-08 11:33',
        ])->execute();

        /** @var \yii\db\Connection $userId Id созданной записи (id пользователя). */
        $userId = $db->getLastInsertID();

        //создать аренду
        $moto_id = 1;
        $db->createCommand()->insert(Rent::tableName(), [
            'moto_id' => $moto_id,
            'user_id' => $userId,
            'date_rent_started' => '2022-03-07 11:33',
            'date_rent_ended' => '2022-03-08 11:33',
            'created_at' => '2022-03-08 11:33',
        ])->execute();
        //конец настройки БД перед запуском теста
        ////////////////////////////

        //ТЕСТ
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
