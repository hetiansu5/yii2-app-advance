<?php
//非框架内置的配置项 -- 适用所有应用当前环境
$mainRedisConfig = [ //redis配置
    'master' => [
        'host' => '127.0.0.1',
        'port' => 6379,
        'timeout' => 1, //s
        'pconnect' => false,
    ],
    'slaves' => [
        [
            'host' => '127.0.0.1',
            'port' => 6379,
            'timeout' => 1, //s
            'pconnect' => false,
        ]
    ],
    'host_to_ip' => false
];

return [
    'memcache.main' => [ //memcache配置
        'servers' => [
            ['host' => '127.0.0.1', 'port' => 11211]
        ],
        'connect_timeout' => 1000, //ms
        'binary_protocol' => true,
    ],

    'redis.main' => $mainRedisConfig, //redis配置

    'counter.main' => $mainRedisConfig, //计数器配置,

    'queue.main' => [  //队列
        'host' => '127.0.0.1',
        'port' => 6379,
        'timeout' => 1, //s
        'pconnect' => false,
    ],
];
