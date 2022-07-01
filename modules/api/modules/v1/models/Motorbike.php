<?php

namespace app\modules\api\modules\v1\models;

use Yii;

/**
 * This is the model class for table "motorbike".
 *
 * @property int $id
 * @property string $model
 * @property string $color
 */
class Motorbike extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'motorbike';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['model', 'color'], 'required'],
            [['model', 'color'], 'string', 'max' => 64],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'model' => 'Model',
            'color' => 'Color',
        ];
    }
}
