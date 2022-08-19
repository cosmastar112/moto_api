<?php

namespace app\modules\api\modules\v1;

use Yii;

/**
 * v1 module definition class
 */
class Module extends \yii\base\Module
{
    /**
     * {@inheritdoc}
     */
    public $controllerNamespace = 'app\modules\api\modules\v1\controllers';

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        parent::init();
        $this->registerTranslations();
        // custom initialization code goes here
    }

    public function registerTranslations()
    {
        Yii::$app->i18n->translations['modules/api/v1/*'] = [
            'class'          => 'yii\i18n\PhpMessageSource',
            'sourceLanguage' => 'en-US',
            'basePath'       => '@app/modules/api/modules/v1/messages',
            'fileMap'        => [
                'modules/api/v1/errors' => 'errors.php',
            ],
        ];
    }

    public static function t($category, $message, $params = [], $language = null)
    {
        return Yii::t('modules/api/v1/' . $category, $message, $params, $language);
    }
}
