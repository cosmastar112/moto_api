<?php

namespace tests\unit\modules\api\modules\v1\models;

use app\modules\api\modules\v1\models\UTCDatetime;

class UTCDatetimeTest extends \Codeception\Test\Unit
{
    public function testNormalize()
    {
        $model = new UTCDatetime();
        $model->datetime = '2022-08-21 12:45:00';
        $model->timezone = 'Europe/Samara';

        $result = $model->normalize();
        $this->assertSame('2022-08-21 08:45:00', $result);
    }

    public function testIncorrectTimezone()
    {
        $model = new UTCDatetime();
        $model->datetime = '2022-08-21 12:45:00';
        $model->timezone = 'IncorrectTimezone'; //неизвестная таймзона

        $result = $model->normalize();
        $this->assertFalse($result);
        $this->assertArrayHasKey('timezone', $model->getErrors());
    }

    public function testIncorrectDatetime()
    {
        $model = new UTCDatetime();
        $model->datetime = 'IncorrectDatetime'; //некорректное время
        $model->timezone = 'Europe/Samara';

        $result = $model->normalize();
        $this->assertFalse($result);
        $this->assertArrayHasKey('datetime', $model->getErrors());
    }
}
