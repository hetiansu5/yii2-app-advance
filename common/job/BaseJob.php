<?php
namespace common\job;

use common\lib\fw\task\RedisJob;

/**
 * 在项目中通过继承RedisJob抽象类,主要实现getConfig()和getManager()
 * 项目中的其他具体Job类继承此类。
 */
abstract class BaseJob extends RedisJob
{
    private static $config = [];

    protected $queueType = 'redis';

    protected function getConfig()
    {
        if (!self::$config) {
            self::$config = \Yii::$app->params['queue.main'];
        }
        return self::$config;
    }

    protected function getManager()
    {
        return Task::getManager();
    }

    public function getQueueType()
    {
        return $this->queueType;
    }

}