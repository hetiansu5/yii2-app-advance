<?php
//非框架内置的配置项 -- 适用所有应用所有环境
return [
    //自定义日志配置
    'log.path' => '/www/privdata/logistics/log/',
    'log.level' => 'info',

    //当前请求时间戳
    'request_time' => isset($_SERVER['REQUEST_TIME']) ? $_SERVER['REQUEST_TIME'] : time(),

];
