<?php
//框架内置的配置项 -- 适用所有应用当前环境
return [
    'components' => [ // 如果是以非Docker方式启动，建议本机配置一下Host  127.0.0.1 host.docker.internal
        'db' => [
            'class' => 'yii\db\Connection',
            'dsn' => 'mysql:host=host.docker.internal;dbname=' . APP_NAME,
            'username' => 'root',
            'password' => '123456',
            'charset' => 'utf8mb4',
        ],
    ],
    'bootstrap' => [
        'debug'
    ],
    'modules' => [
        'debug' => [
            'class' => 'yii\debug\Module',
        ],
    ]
];
