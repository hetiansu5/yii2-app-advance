<?php
//框架内置的配置项 -- 适用所有应用当前环境
return [
    'components' => [
        'db' => [
            'class' => 'yii\db\Connection',
            'dsn' => 'mysql:host=mysql.m.com;dbname=' . APP_NAME,
            'username' => 'project',
            'password' => 'Wlb43iPe872mkqw',
            'charset' => 'utf8mb4',
        ],
    ],
];
