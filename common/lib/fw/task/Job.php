<?php
namespace common\lib\fw\task;

abstract class Job
{
    //----需要子类进一步详细配置的属性

    //必须设置的
    protected $jobName;

    //可选设置的
    protected $workerSleepMilliseconds = 100; //优先使用
    protected $workerSleepSeconds = 1; //如果$workerSleepMilliseconds为0时，才起作用（兼容之前版本）
    protected $workerSleepLoops = 100;
    protected $isRunningLimit = 1800; //运行30分钟后需要重启进程

    //end--需要子类进一步详细配置的属性


    private $startTime = 0;
    private $workerSleepMicroseconds = 0;
    private $checkStopTime = 0;
    private $checkStopInterval = 2;
    private $needStopResultCache = null; //缓存读取needStop配置信息的数据

    protected $dequeueConfig; //根据不同类型的Job赋予不同的参数

    public $id; //任务ID
    public $message;
    public $time = 0; //任务入队时间
    public $executeTime = 0; //任务设定要执行的时间

    public function __construct($message = [])
    {
        if ($message) {
            $this->id = substr(md5(uniqid(gethostname(), true)), 0, 16);
            $currentTime = time();
            $this->time = $currentTime;
            $this->executeTime = $currentTime;
            $this->message = $message;
        }

        if ($this->workerSleepMilliseconds) {
            $this->workerSleepMicroseconds = $this->workerSleepMilliseconds * 1000;
        } elseif ($this->workerSleepSeconds) {
            $this->workerSleepMicroseconds = $this->workerSleepSeconds * 1000000;
        }
    }

    public function send()
    {
        return $this->getQueue()->enqueue($this);
    }

    public function work()
    {
        $this->startTime = time();
        $queue = $this->getQueue();
        $loops = 0;
        while (1) {
            //根据配置判断是否退出
            if ($this->needStop()) {
                return;
            }

            /** @var Job $job */
            $job = $queue->dequeue($this->dequeueConfig);
            if ($job) {
                $this->perform($job);
            } else {
                //拿不到数据的时候休眠500ms
                usleep(500000);
            }

            $loops++;
            if ($this->workerSleepLoops && $this->workerSleepMicroseconds && $loops % $this->workerSleepLoops == 0) {
                usleep($this->workerSleepMicroseconds);
            }
        }
    }

    public function getJobName()
    {
        return $this->jobName;
    }

    protected function needStop()
    {
        $currentTime = time();
        if ($this->startTime <= 0) {
            $this->startTime = $currentTime;
        }
        if ($currentTime - $this->startTime > $this->isRunningLimit) {
            return true;
        }
        if ($this->checkStopTime && $currentTime <= $this->checkStopTime + $this->checkStopInterval) {
            return $this->needStopResultCache;
        }
        $this->checkStopTime = $currentTime;
        /** @var Manager $manager */
        $manager = $this->getManager();
        $taskConfig = $manager->getTaskConfig($this->jobName);
        if (!$manager->isTaskRun($this->jobName, $taskConfig)
            || $manager->getWorkerNum($this->jobName, $taskConfig) <= 0
            || $manager->getTaskRestartTime($this->jobName, $taskConfig) > $this->startTime
        ) {
            $this->needStopResultCache = true;
        } else {
            $this->needStopResultCache = false;
        }
        return $this->needStopResultCache;
    }


    /**
     * @return QueueInterface
     */
    abstract public function getQueue();

    abstract protected function getConfig();

    abstract protected function getManager();

    abstract protected function perform($job);


    public function toJson()
    {
        $arr = [
            'c' => get_called_class(),
            'id' => $this->id,
            't' => $this->time,
            'et' => $this->executeTime,
            'msg' => $this->message
        ];
        return json_encode($arr);
    }

    /**
     * @param $json
     * @return null|array
     */
    public static function toObject($json)
    {
        if (!$json) {
            return null;
        }
        return json_decode($json, true);
    }

}
