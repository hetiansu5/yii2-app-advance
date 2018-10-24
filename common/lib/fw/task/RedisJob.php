<?php
namespace common\lib\fw\task;

abstract class RedisJob extends Job
{
    //----需要子类进一步详细配置的属性

    //必须设置的
    protected $jobName;

    //可选设置的
    protected $workerSleepSeconds = 1;
    protected $workerSleepLoops = 100;
    protected $isRunningLimit = 1800; //运行30分钟后需要重启进程
    protected $isBlockingDequeue = false;
    protected $dequeueTimeout = 5;

    //end--需要子类进一步详细配置的属性

    protected $queue;

    public function __construct($message = [])
    {
        parent::__construct($message);

        $this->dequeueConfig = [
            'is_blocking' => $this->isBlockingDequeue,
            'timeout' => $this->dequeueTimeout
        ];
    }

    public function getQueue()
    {
        if (!$this->queue) {
            //在RedisQueue中queueName等于jobName
            $this->queue = new RedisQueue($this->jobName, $this->getConfig());
        }
        return $this->queue;
    }

}