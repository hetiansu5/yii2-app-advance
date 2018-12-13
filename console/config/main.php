<?php
//框架内置的配置项 -- 适用当前应用所有环境
$config = [
    'id' => 'app-console',
    'basePath' => dirname(__DIR__),
    'controllerNamespace' => 'console\controllers',
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
    'language' => 'en-US', //默认语言
    'bootstrap' => [ //引导组件
        'log'
    ],
    'components' => [
        'log' => [ //日志 https://www.yiichina.com/doc/guide/2.0/runtime-logging?language=zh-cn
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'common\lib\FileTarget', //指定日志插件
                    //以下的参数键都是类本身支持的公共属性
                    'levels' => ['error', 'warning'], //指定哪些告警级别的消息被处理
                    'logVars' => [], //指定追加的上下文信息，空数组代表不追加任何上下文
                    'logFile' => '/www/privdata/' . APP_NAME . '/log/app.log', //日志存放目录
                    'fileMode' => 0777, //日志权限
                ],
            ],
        ],
        'errorHandler' => [
            'class' => 'console\components\ErrorHandler', //自定义错误处理类
        ],
    ]
];

$config = yii\helpers\ArrayHelper::merge(
    //console模式忽略公共的config
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
