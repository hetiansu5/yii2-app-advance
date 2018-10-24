<?php
namespace common\job;

use common\lib\fw\task\Job;
use common\lib\fw\task\Manager;

/**
 * 在该类中定义具体JOB_NAME,
 * 并在该类中实现getJobList()和getManager()
 */
class Task
{

    const JOB_NAME_TEST_EVENT = 'test_event'; //测试

    private static $jobList;
    private static $managerRedisConfig;

    /**
     * @return Job[]
     */
    public static function getJobList()
    {
        if (!self::$jobList) {
            self::$jobList = [
                self::JOB_NAME_TEST_EVENT => TestEventJob::class,
            ];
        }
        return self::$jobList;
    }

    public static function getManager()
    {
        if (!self::$managerRedisConfig) {
            self::$managerRedisConfig = \Yii::$app->params['queue.main'];
        }
        return Manager::getInstance(self::$managerRedisConfig);
    }
}