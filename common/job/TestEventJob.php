<?php
namespace common\job;

use common\constants\LogType;
use common\lib\Functions;
use common\lib\fw\Logger;
use common\models\order\LogisOrderModel;

/**
 * 测试
 * @author hts
 */
class TestEventJob extends BaseJob
{

    protected $jobName = Task::JOB_NAME_TEST_EVENT;

    protected function perform($job)
    {
        $orderId = 49;
        Logger::getInstance()->error(Functions::toString($orderId) . 'begin', LogType::JOB_ORDER);
    }

}