<?php
//控制器配置
return [
    'id' => 'login',
    'name' => '登录',
    'actions' => [
        'index' => [
            'name' => '登录页'
        ],
        'verify-code' => [
            'name' => '验证码'
        ],
        'auth' => [
            'name' => '登录认证'
        ],
    ]
];