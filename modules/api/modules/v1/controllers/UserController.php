<?php

namespace app\modules\api\modules\v1\controllers;

use Yii;
use yii\rest\ActiveController;
use yii\web\Response;
use app\modules\api\modules\v1\models\User;

class UserController extends ActiveController
{
    public $modelClass = 'app\modules\api\modules\v1\models\User';

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['contentNegotiator']['formats'] = ['application/json' => Response::FORMAT_JSON];
        return $behaviors;
    }

    public function actions()
    {
        $actions = parent::actions();

        //отключить дефолтное действие create, переопределить
        unset($actions['create']);

        return $actions;
    }

    /**
     * @OA\Post(
     *     path="/api/v1/user/create",
     *     summary="Создать пользователя",
     *     description="Создать пользователя",
     *     tags={"user"},
     *     @OA\RequestBody(
     *         description="Пользователь",
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     property="username",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="email",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="fio",
     *                     type="string"
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Результат создания пользователя",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 type="object",
     *                 @OA\Property(
     *                     property="id",
     *                     type="integer"
     *                 ),
     *                 @OA\Property(
     *                     property="username",
     *                     type="integer"
     *                 ),
     *                 @OA\Property(
     *                     property="email",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="fio",
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
    //create
    public function actionCreate()
    {
        $bodyParams = Yii::$app->getRequest()->getBodyParams();

        $model = new User();
        $model->username = !empty($bodyParams) && isset($bodyParams['username']) ? $bodyParams['username'] : null;
        $model->email = !empty($bodyParams) && isset($bodyParams['email']) ? $bodyParams['email'] : null;
        $model->fio = !empty($bodyParams) && isset($bodyParams['fio']) ? $bodyParams['fio'] : null;

        if (!$model->save()) {
            $errors = implode('; ', $model->getErrorSummary(true));
            throw new \yii\web\BadRequestHttpException($errors);
        }

        return [
            'message' => 'OK',
            'code' => 200,
            'data' => [
                'id' => $model->id,
                'username' => $model->username,
                'email' => $model->email,
                'fio' => $model->fio,
                'created_at' => $model->created_at,
            ]
        ];
    }
}