<?php

namespace app\modules\api\modules\v1\controllers;

use yii\rest\ActiveController;
use yii\web\Response;

class MotoController extends ActiveController
{
    public $modelClass = 'app\modules\api\modules\v1\models\Motorbike';

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['contentNegotiator']['formats'] = ['application/json' => Response::FORMAT_JSON];
        return $behaviors;
    }
}