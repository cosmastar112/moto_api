<?php

$params = require __DIR__ . '/params.php';
$db = require __DIR__ . '/db.php';
$test_db = require __DIR__ . '/test_db.php';

$config = [
    'id' => 'basic-console',
    'timeZone' => 'Europe/Moscow',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'controllerNamespace' => 'app\commands',
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
        '@tests' => '@app/tests',
    ],
    'components' => [
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'log' => [
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'db' => $db,
        'test_db' => $test_db,
    ],
    'params' => $params,
    'controllerMap' => [
        'migrate-api-v1' => [
            'class' => 'yii\console\controllers\MigrateController',
            'migrationPath' => '@app/modules/api/modules/v1/migrations',
            'migrationTable' => 'migration_api_v1',
        ],
        /*
        'fixture' => [ // Fixture generation command line.
            'class' => 'yii\faker\FixtureController',
        ],
        */
    ],
];

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
    ];
}

return $config;
