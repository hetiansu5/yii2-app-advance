<?php

namespace backend\controllers;

use backend\components\BaseController;
use common\job\BaseJob;
use common\job\Task;
use common\lib\fw\task\Manager;

class TaskController extends BaseController
{

    protected $curModule = 'setting'; //必要。菜单有模块之分

    public function actionIndex()
    {
        $jobList = Task::getJobList();
        $manager = Task::getManager();

        $data = [];
        foreach ($jobList as $jobName => $jobClassName) {
            /** @var BaseJob $job */
            $job = new $jobClassName();
            $taskConfig = $manager->getTaskConfig($jobName);
            $data[] = [
                'job_name' => $jobName,
                'queue_type' => $job->getQueueType(),
                'is_run' => isset($taskConfig[Manager::RUNNING_SWITCH_KEY]) ? (bool)$taskConfig[Manager::RUNNING_SWITCH_KEY] : false,
                'restart_time' => isset($taskConfig[Manager::RESTART_TIME_KEY]) ? (int)$taskConfig[Manager::RESTART_TIME_KEY] : 0,
                'worker_num' => isset($taskConfig[Manager::WORKER_NUM_KEY]) ? (int)$taskConfig[Manager::WORKER_NUM_KEY] : 0,
                'backlog' => $job->getQueue()->size(),
            ];
        }

        return $this->_assign('data', $data)
            ->_render();
    }

    public function actionRestartAllTasks()
    {
        $manager = Task::getManager();
        $jobList = Task::getJobList();
        $jobNames = array_keys($jobList);
        if ($jobNames) {
            $currentTime = time();
            foreach ($jobNames as $jobName) {
                if (!$manager->setTaskRestartTime($jobName, $currentTime)) {
                    $this->ajaxError();
                }
            }
        }
        $this->ajaxSuccess();
    }

    public function actionRestartDaemon()
    {
        $manager = Task::getManager();
        if (!$manager->setDaemonRestartTime(time())) {
            $this->ajaxError();
        }
        $this->ajaxSuccess();
    }

    public function actionRestartTask()
    {
        $jobName = trim($this->request->post('job_name'));
        if (!$jobName) {
            $this->ajaxError('job_name不能为空');
        }
        $manager = Task::getManager();
        if (!$manager->setTaskRestartTime($jobName, time())) {
            $this->ajaxError();
        }
        $this->ajaxSuccess();
    }

    public function actionSetAllTasksRunningSwitch()
    {
        $isRun = $this->request->post('is_run') > 0 ? 1 : 0;
        $manager = Task::getManager();
        $jobNames = array_keys(Task::getJobList());
        if ($jobNames) {
            foreach ($jobNames as $jobName) {
                if (!$manager->setTaskRunningSwitch($jobName, $isRun)) {
                    $this->ajaxError();
                }
            }
        }
        $this->ajaxSuccess();
    }

    public function actionSetTaskRunningSwitch()
    {
        $jobName = trim($this->request->post('job_name'));
        if (!$jobName) {
            $this->ajaxError('job_name不能为空');
        }
        $isRun = $this->request->post('is_run') > 0 ? 1 : 0;
        $manager = Task::getManager();
        if (!$manager->setTaskRunningSwitch($jobName, $isRun)) {
            $this->ajaxError();
        }
        $this->ajaxSuccess();
    }

    public function actionSetWorkerNum()
    {
        $jobName = trim($this->request->post('job_name'));
        if (!$jobName) {
            $this->ajaxError('job_name不能为空');
        }
        $workerNum = intval($this->request->post('worker_num'));
        $manager = Task::getManager();
        if (!$manager->setWorkerNum($jobName, $workerNum)) {
            $this->ajaxError();
        }
        $this->ajaxSuccess();
    }

}
