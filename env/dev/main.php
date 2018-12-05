<?php
//框架内置的配置项 -- 适用所有应用当前环境
return [
    'components' => [
        'db' => [
            'class' => 'yii\db\Connection',
            'dsn' => 'mysql:host=127.0.0.1;dbname=' . APP_NAME,
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
