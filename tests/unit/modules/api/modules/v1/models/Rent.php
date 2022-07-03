<?php

namespace tests\unit\modules\api\modules\v1\models;

class Rent extends \app\modules\api\modules\v1\models\Rent
{
    private static $test_db;

    public static function getDb()
    {
        if (empty(self::$test_db)) {

            //создать БД
            self::$test_db = new \yii\db\Connection([
                'dsn' => 'mysql:host=db;dbname=moto_test',
                'username' => 'moto',
                'password' => 'password',
                'charset' => 'utf8'
            ]);
        }

        return self::$test_db;
    }
}
