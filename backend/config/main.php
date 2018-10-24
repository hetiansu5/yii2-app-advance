<?php
//框架内置的配置项 -- 适用当前应用所有环境
$config = [
    'id' => 'app-backend',
    'basePath' => dirname(__DIR__),
    'controllerNamespace' => 'backend\controllers',
    'components' => [
        'request' => [
            'class' => 'yii\web\Request',
            'csrfParam' => '_csrf-backend',
            'cookieValidationKey' => 'j1m7zFSqH7bpTns_36yWiQHKLKEveR70',
            'enableCsrfValidation' => false,
        ],
        'user' => [
            'identityClass' => 'common\models\User',
            'enableAutoLogin' => true,
            'identityCookie' => ['name' => '_identity-backend', 'httpOnly' => true],
        ],
        'session' => [
            // this is the name of the session cookie used for login on the backend
            'name' => 'advanced-backend',
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
    ]
];

$config = yii\helpers\ArrayHelper::merge(
    require __DIR__ . '/../../common/config/main.php',
    $config,
    require __DIR__ . '/../../env/' . YII_ENV . '/main.php'
);

$params = array_merge(
    require __DIR__ . '/../../common/config/params.php',
    require __DIR__ . '/params.php',
    require __DIR__ . '/../../env/' . YII_ENV . '/params.php'
);

$config['params'] = $params;

return $config;
