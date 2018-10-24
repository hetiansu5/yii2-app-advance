<?php
//pre测试环境
defined('YII_ENV_PRE') or define('YII_ENV_PRE', YII_ENV === 'pre');

//根据不同应用进行设备
defined('APP_NAME') or define('APP_NAME', 'frameworks');

//注册公共的namespace
Yii::setAlias('@common', dirname(__DIR__));


