<?php

namespace tests\unit\models;

use app\models\GMTDatetime;

class GMTDatetimeTest extends \Codeception\Test\Unit
{
    public function testNormalizeGmtOffsetOutOfBounds_up()
    {
        $model = new GMTDatetime();
        $model->datetime = '2022-07-13 14:57:00';
        $model->gmtOffset = 13;

        $result = $model->normalize();
        $this->assertFalse($result);
        $this->assertArrayHasKey('gmtOffset', $model->getErrors());
    }

    public function testNormalizeGmtOffsetOutOfBounds_down()
    {
        $model = new GMTDatetime();
        $model->datetime = '2022-07-13 14:57:00';
        $model->gmtOffset = -13;

        $result = $model->normalize();
        $this->assertFalse($result);
        $this->assertArrayHasKey('gmtOffset', $model->getErrors());
    }

    public function testNormalizePositive()
    {
        $model = new GMTDatetime();
        $model->datetime = '2022-07-13 14:57:00';
        $model->gmtOffset = 4;
        $result = $model->normalize();
        $this->assertSame('2022-07-13 10:57:00', $result);
    }

    public function testNormalizeNegative()
    {
        $model = new GMTDatetime();
        $model->datetime = '2022-07-13 14:57:00';
        $model->gmtOffset = -4;
        $result = $model->normalize();
        $this->assertSame('2022-07-13 18:57:00', $result);
    }
}
