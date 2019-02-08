<?php
use common\job\Task;
use common\lib\fw\task\Manager;

/**
 * 【demo】
 * work的守护进程
 */

//设置告警级别 -- 屏蔽Notice、Warning的告警
error_reporting(E_ALL  & ~E_NOTICE & ~E_WARNING);

//定义当前部署环境 local-本地开发  dev-测试环境  prod-线上环境
define('YII_ENV', isset($_SERVER['YII_ENV']) ? $_SERVER['YII_ENV'] : 'prod');

//是否启用调试模式   默认线上不启用
define('YII_DEBUG', YII_ENV != 'prod' ? true : false);

//composer autoload文件
require __DIR__ . '/../../vendor/autoload.php';

//Yii框架核心代码
require __DIR__ . '/../../vendor/yiisoft/yii2/Yii.php';

//公共的引导程序
require __DIR__ . '/../../common/config/bootstrap.php';

//当前应用的引导程序
require __DIR__ . '/../config/bootstrap.php';

//配置
$config = require __DIR__ . '/../config/main.php';

$application = new yii\console\Application($config);

$phpCmd = 'php';
$yiiCmd = $application->getBasePath() . '/web/yii';

$startTime = time();
$manager = Task::getManager();
$jobList = Task::getJobList();
$count = 0;
while (1) {
    sleep(3);
    $count++;

    //是否需要重启daemon自身
    if ($manager->getDaemonRestartTime() > $startTime) {
        exec($phpCmd . ' ' . __FILE__ . ' >> /dev/null 2>&1 &');
        exit(1);
    }

    foreach ($jobList as $jobName => $jobClassName) {
        //扫描doingQueue中过期的job(采用redis queue才有需要)
        $obj = new $jobClassName();

        //载入任务配置(配置某任务是否启动,要启动几个进程等信息)
        $jobConf = $manager->getTaskConfig($jobName);
        $workerUri = 'task/work ' . $jobName;
        $cmd = $yiiCmd . ' ' . $workerUri;
        //使用ps命令做进程匹配时,最后一个grep的值后面加上$,避免错误匹配
        $processCount = intval(exec('ps ax|grep -v " grep"|grep "php"|grep "' . $cmd . '$"|wc -l'));
        $isRunning = isset($jobConf[Manager::RUNNING_SWITCH_KEY]) ? (bool)$jobConf[Manager::RUNNING_SWITCH_KEY] : false;
        $workerNum = $isRunning && isset($jobConf[Manager::WORKER_NUM_KEY]) ? (int)$jobConf[Manager::WORKER_NUM_KEY] : 0;
        $diff = $workerNum - $processCount;
        if ($diff > 0) {
            //若实际运行的进程数少于配置的进程数,把少掉的补上
            for ($i = 0; $i < $diff; $i++) {
                exec($cmd . '  >> /dev/null 2>&1 &');
            }
        }
    }

}