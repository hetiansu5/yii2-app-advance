<?php

namespace console\controllers;

use common\job\Task;
use common\lib\fw\task\Job;
use yii\console\Controller;

/**
 * 执行job任务
 * @author hts
 */
class TaskController extends Controller
{

    public function actionWork($jobName)
    {
        if ($jobName) {
            $jobList = Task::getJobList();
            if (isset($jobList[$jobName])) {
                /** @var Job $obj */
                $obj = new $jobList[$jobName]();
                $obj->work();
            }
        }
    }

}
