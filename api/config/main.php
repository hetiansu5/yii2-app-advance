<?php
//框架内置的配置项 -- 适用当前应用所有环境
$config = [
    'id' => 'app-api',
    'basePath' => dirname(__DIR__),
    'controllerNamespace' => 'api\controllers',
    'components' => [
        'request' => [
            'csrfParam' => '_csrf-api',
            'cookieValidationKey' => 'j1m7zFSqH7bpTns_36yWiQHKLKEveR70',
            'enableCsrfValidation' => false,
        ],
        'session' => [
            // this is the name of the session cookie used for login on the customer
            'name' => 'advanced-api',
        ],
        'errorHandler' => [
            'class' => 'common\components\ErrorHandler', //自定义错误处理类
        ],
    ],
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
