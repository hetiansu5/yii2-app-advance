<?php
//框架内置的配置项 -- 适用所有应用所有环境
return [
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm' => '@vendor/npm-asset',
    ],
    'bootstrap' => [ //引导组件
        'log' //log 组件必须在 bootstrapping 期间就被加载，以便于它能够及时调度日志消息到目标里
    ],
    'modules' => [],
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
    'language' => 'zh-CN', //默认语言
    'components' => [ //组件
        'urlManager' => [ //路由
            'enablePrettyUrl' => true, //开启下划线的路由访问模式
            'showScriptName' => false,
            'rules' => [
            ],
        ],
        'i18n' => [ //多语言配置
            'translations' => [
                'error' => [
                    'class' => 'yii\i18n\PhpMessageSource',
                    //Yii的多语言设计不好的地方，key使用默认语言，只有发现非默认语言才会转换，所以这里设计一个不存在的语言
                    'sourceLanguage' => 'no_exist',
                    'basePath' => '@common/messages',
                ],
            ],
        ],
        'log' => [ //日志 https://www.yiichina.com/doc/guide/2.0/runtime-logging?language=zh-cn
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget', //指定日志插件
                    //以下的参数键都是类本身支持的公共属性
                    'levels' => ['error', 'warning'], //指定哪些告警级别的消息被处理
                    'logVars' => [], //指定追加的上下文信息，空数组代表不追加任何上下文
                    'prefix' => function ($message) { //自定义消息前缀，增加用户ID和访问的控制器方法
                        $userIp = \Yii::$app->request->getUserIP();
                        $application = \Yii::$app->id;
                        $controller = \Yii::$app->controller->id;
                        $action = \Yii::$app->controller->action->id;
                        return "[{$application}][{$controller}-{$action}][{$userIp}]";
                    },
                    'logFile' => '/www/privdata/' . APP_NAME . '/log/app.log', //日志存放目录
                    'fileMode' => 0777, //日志权限
                ],
            ],
        ],
    ],
];
