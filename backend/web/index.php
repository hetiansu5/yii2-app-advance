<?php

//设置时区 -- 默认欧洲格林威治标准时间时间
date_default_timezone_set('Europe/Berlin');

//设置告警级别 -- 屏蔽Notice、Warning的告警
error_reporting(E_ALL  & ~E_NOTICE & ~E_WARNING);

//定义当前部署环境 local-本地开发  dev-测试环境  prod-线上环境
define('YII_ENV', isset($_SERVER['YII_ENV']) ? $_SERVER['YII_ENV'] : 'prod');

//是否启用调试模式   默认线上不启用
define('YII_DEBUG', YII_ENV != 'prod' ? true : false);

//composer autoload文件
require __DIR__ . '/../../vendor/autoload.php';

//Yii框架核心代码
require __DIR__ . '/../../vendor/yiisoft/yii2/Yii.php';

//公共的引导程序
require __DIR__ . '/../../common/config/bootstrap.php';

//当前应用的引导程序
require __DIR__ . '/../config/bootstrap.php';

//配置
$config = require __DIR__ . '/../config/main.php';

//实例化应用类-运行
(new yii\web\Application($config))->run();
