<?php
namespace common\lib\fw\task;

use common\constants\Common;
use common\lib\fw\InstanceTrait;
use common\lib\fw\Redis;

class Manager
{
    use InstanceTrait {
        getInstance as _getInstance;
    }

    private $redis;

    const RESTART_TIME_KEY = Common::PROJECT_KEY . ':restart_time';
    const RUNNING_SWITCH_KEY = Common::PROJECT_KEY . ':run';
    const WORKER_NUM_KEY = Common::PROJECT_KEY . ':worker_num';

    private function __construct(array $config)
    {
        $this->redis = Redis::getInstance($config);
    }

    /**
     * @param array $config
     * @return self
     */
    public static function getInstance(array $config)
    {
        return self::_getInstance($config);
    }

    private function getTaskConfigKey($jobName)
    {
        return Common::PROJECT_KEY . ':task_conf:' . $jobName;
    }

    private function getDaemonRestartTimeKey()
    {
        return Common::PROJECT_KEY . ':task_daemon_restart_time';
    }

    public function getTaskConfig($jobName)
    {
        //data structure: hash set
        return $this->redis->hGetAll($this->getTaskConfigKey($jobName));
    }

    public function setTaskConfig($jobName, array $conf)
    {
        return $this->redis->hMset($this->getTaskConfigKey($jobName), $conf);
    }

    public function getTaskRestartTime($jobName, $taskConfig = null)
    {
        if (!$taskConfig) {
            $taskConfig = $this->getTaskConfig($jobName);
        }
        return isset($taskConfig[self::RESTART_TIME_KEY]) ? (int)$taskConfig[self::RESTART_TIME_KEY] : 0;
    }

    public function setTaskRestartTime($jobName, $restartTime)
    {
        return $this->redis->hSet($this->getTaskConfigKey($jobName), self::RESTART_TIME_KEY, $restartTime) !== false;
    }

    public function isTaskRun($jobName, $taskConfig = null)
    {
        if (!$taskConfig) {
            $taskConfig = $this->getTaskConfig($jobName);
        }
        return isset($taskConfig[self::RUNNING_SWITCH_KEY]) ? (bool)$taskConfig[self::RUNNING_SWITCH_KEY] : false;
    }

    public function setTaskRunningSwitch($jobName, $isRun)
    {
        return $this->redis->hSet($this->getTaskConfigKey($jobName), self::RUNNING_SWITCH_KEY, (bool)$isRun) !== false;
    }

    public function getWorkerNum($jobName, $taskConfig = null)
    {
        if (!$taskConfig) {
            $taskConfig = $this->getTaskConfig($jobName);
        }
        return isset($taskConfig[self::WORKER_NUM_KEY]) ? (int)$taskConfig[self::WORKER_NUM_KEY] : 0;
    }

    public function setWorkerNum($jobName, $workerNum)
    {
        return $this->redis->hSet($this->getTaskConfigKey($jobName), self::WORKER_NUM_KEY, $workerNum) !== false;
    }

    public function getDaemonRestartTime()
    {
        return (int)$this->redis->get($this->getDaemonRestartTimeKey());
    }

    public function setDaemonRestartTime($restartTime)
    {
        return $this->redis->set($this->getDaemonRestartTimeKey(), $restartTime);
    }

}