<?php

namespace app\modules\api\modules\v1\controllers;

use Yii;
use yii\rest\ActiveController;
use yii\web\Response;
use yii\data\ActiveDataProvider;
use app\modules\api\modules\v1\models\Rent;

class MotoController extends ActiveController
{
    public $modelClass = 'app\modules\api\modules\v1\models\Motorbike';

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['contentNegotiator']['formats'] = ['application/json' => Response::FORMAT_JSON];
        return $behaviors;
    }

    //get-rents
    public function actionGetRents()
    {
        $requestParams = Yii::$app->getRequest()->getBodyParams();
        if (empty($requestParams)) {
            $requestParams = Yii::$app->getRequest()->getQueryParams();
        }

        $query = Rent::find();
        if (!empty($requestParams) && isset($requestParams['moto_id'])) {
            $query->andWhere(['moto_id' => $requestParams['moto_id']]);
        }

        return Yii::createObject([
            'class' => ActiveDataProvider::className(),
            'query' => $query,
        ]);
    }
}