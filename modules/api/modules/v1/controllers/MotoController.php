<?php

namespace app\modules\api\modules\v1\controllers;

use Yii;
use yii\rest\ActiveController;
use yii\web\Response;
use yii\data\ActiveDataProvider;
use app\modules\api\modules\v1\models\Rent;
use app\modules\api\modules\v1\models\Motorbike;
use app\modules\api\modules\v1\Module;

/**
 * @OA\Info(
 *     title="Moto API",
 *     version="1"
 * )
 */
class MotoController extends ActiveController
{
    public $modelClass = 'app\modules\api\modules\v1\models\Motorbike';

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['contentNegotiator']['formats'] = ['application/json' => Response::FORMAT_JSON];
        return $behaviors;
    }

    /**
     * @OA\Get(
     *     path="/api/v1/moto/{moto_id}",
     *     summary="Список аренд мотоцикла по его ID",
     *     description="Получение списка аренд указанного мотоцикла",
     *     tags={"moto"},
     *     @OA\Parameter(
     *         description="ID мотоцикла",
     *         in="path",
     *         name="moto_id",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             format="int64"
     *         )
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Cписок аренд указанного мотоцикла",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 type="array",
     *                 @OA\Items(
     *                     @OA\Property(
     *                         property="id",
     *                         type="integer"
     *                     ),
     *                     @OA\Property(
     *                         property="username",
     *                         type="string"
     *                     ),
     *                     @OA\Property(
     *                         property="moto_id",
     *                         type="integer"
     *                     ),
     *                     @OA\Property(
     *                         property="date_rent_started",
     *                         type="string"
     *                     ),
     *                     @OA\Property(
     *                         property="date_rent_ended",
     *                         type="string"
     *                     ),
     *                     @OA\Property(
     *                         property="created_at",
     *                         type="string"
     *                     ),
     *                     @OA\Property(
     *                         property="updated_at",
     *                         type="string"
     *                     )
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(response="404", description="Мотоцикл не найден")
     * )
     */
    //get-rents
    public function actionGetRents()
    {
        $requestParams = Yii::$app->getRequest()->getQueryParams();

        /** @var string $moto_id Идентификатор мотоцикла */
        $moto_id = isset($requestParams['moto_id']) ? $requestParams['moto_id'] : null;
        if (is_null($moto_id) || is_null(Motorbike::findOne($moto_id))) {
            throw new \yii\web\NotFoundHttpException(Module::t('errors', 'Motorbike not found'));
        }

        /** @var \yii\db\QueryInterface $query Объект запроса. */
        $query = Rent::find()
            ->andWhere(['moto_id' => $requestParams['moto_id']]);

        return Yii::createObject([
            'class' => ActiveDataProvider::className(),
            'query' => $query,
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/v1/moto/{moto_id}/rent",
     *     summary="Арендовать мотоцикл",
     *     description="Создание аренды мотоцикла на указанный интервал времени (в запросе должна быть указана таймзона).

Время указывается в формате ""Год-Месяц-Дата Часы:Минуты:Секунды"" в GMT с поправкой на таймзону.

Таймзона может принимать значение [-12; 12].

Например: клиент из Москвы указывает время ""2022-03-07 14:33:00"" и таймзону 3 (т.к. часовой пояс Москвы GMT+3). В результате преобразований указанное время будет равно ""2022-03-07 11:33:00"" (GMT).",
     *     tags={"moto"},
     *     @OA\Parameter(
     *         description="ID мотоцикла",
     *         in="path",
     *         name="moto_id",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             format="int64"
     *         )
     *     ),
     *     @OA\RequestBody(
     *         description="Аренда",
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     property="username",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="date_rent_started",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="date_rent_ended",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="timezone",
     *                     type="integer"
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Результат создания аренды",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 type="object",
     *                 @OA\Property(
     *                     property="id",
     *                     type="integer"
     *                 ),
     *                 @OA\Property(
     *                     property="moto_id",
     *                     type="integer"
     *                 ),
     *                 @OA\Property(
     *                     property="username",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="date_rent_started",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="date_rent_ended",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="created_at",
     *                     type="string"
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(response="400", description="Данные не прошли валидацию")
     * )
     */
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