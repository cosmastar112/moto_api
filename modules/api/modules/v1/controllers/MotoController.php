<?php

namespace app\modules\api\modules\v1\controllers;

use Yii;
use yii\rest\ActiveController;
use yii\web\Response;
use yii\data\ActiveDataProvider;
use app\modules\api\modules\v1\models\Rent;
use app\modules\api\modules\v1\models\Motorbike;

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
        $requestParams = Yii::$app->getRequest()->getQueryParams();

        /** @var string $moto_id Идентификатор мотоцикла */
        $moto_id = isset($requestParams['moto_id']) ? $requestParams['moto_id'] : null;
        if (is_null($moto_id) || is_null(Motorbike::findOne($moto_id))) {
            throw new \yii\web\NotFoundHttpException('Мотоцикл не найден');
        }

        /** @var \yii\db\QueryInterface $query Объект запроса. */
        $query = Rent::find()
            ->andWhere(['moto_id' => $requestParams['moto_id']]);

        return Yii::createObject([
            'class' => ActiveDataProvider::className(),
            'query' => $query,
        ]);
    }

    //create-rent
    public function actionCreateRent()
    {
        $queryParams = Yii::$app->getRequest()->getQueryParams();
        $bodyParams = Yii::$app->getRequest()->getBodyParams();

        $rent = new Rent();
        $rent->moto_id = !empty($queryParams) && isset($queryParams['moto_id']) ? $queryParams['moto_id'] : null;
        $rent->username = !empty($bodyParams) && isset($bodyParams['username']) ? $bodyParams['username'] : null;
        $rent->date_rent_started = !empty($bodyParams) && isset($bodyParams['date_rent_started']) ? $bodyParams['date_rent_started'] : null;
        $rent->date_rent_ended = !empty($bodyParams) && isset($bodyParams['date_rent_ended']) ? $bodyParams['date_rent_ended'] : null;
        $rent->timezone = !empty($bodyParams) && isset($bodyParams['timezone']) ? $bodyParams['timezone'] : null;

        if (!$rent->save()) {
            $errors = implode('; ', $rent->getErrorSummary(true));
            throw new \yii\web\BadRequestHttpException($errors);
        }

        return [
            'message' => 'OK',
            'code' => 200,
            'data' => [
                'id' => $rent->id,
                'moto_id' => $rent->moto_id,
                'username' => $rent->username,
                'date_rent_started' => $rent->date_rent_started,
                'date_rent_ended' => $rent->date_rent_ended,
                'created_at' => $rent->created_at,
            ]
        ];
    }
}