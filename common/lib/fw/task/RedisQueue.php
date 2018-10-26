<?php
namespace common\lib\fw\task;

use common\lib\fw\Redis;

class RedisQueue implements QueueInterface
{
    private $redis;
    private $redisKeyPrefix = 't-q:'; //队列Job的前缀key
    private $queueName = '';

    public function __construct($queueName, array $config)
    {
        $this->queueName = $queueName;
        $this->redis = Redis::getInstance($config);
    }

    public function enqueue(Job $job)
    {
        return $this->redis->lPush($this->getQueueKey(), $job->toJson());
    }

    public function dequeue(array $config = [])
    {
        $isBlocking = isset($config['is_blocking']) ? (bool)$config['is_blocking'] : false;
        $timeout = isset($config['timeout']) ? (int)$config['timeout'] : 0;
        if ($isBlocking) {
            $json = $this->redis->brPop($this->getQueueKey(), $timeout);
        } else {
            $json = $this->redis->rPop($this->getQueueKey());
        }
        $job = Job::toObject($json);
        if (!$job) {
            return false;
        }
        return $job;
    }

    public function reverseEnqueue(Job $job)
    {
        return $this->redis->rPush($this->getQueueKey(), $job->toJson());
    }

    public function size()
    {
        return (int)$this->redis->lLen($this->getQueueKey());
    }


    private function getQueueKey()
    {
        //data structure: list
        return $this->redisKeyPrefix . $this->queueName;
    }
}