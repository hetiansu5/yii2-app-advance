<?php
namespace common\lib\fw\task;

interface QueueInterface
{
    public function __construct($queueName, array $config);
    public function enqueue(Job $job);
    public function dequeue(array $config = []);
    public function reverseEnqueue(Job $job);
    public function size();
}