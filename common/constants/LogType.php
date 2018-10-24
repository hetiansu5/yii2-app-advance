<?php
namespace common\constants;

/**
 * 日志类型常量
 */
class LogType extends \common\lib\fw\LogType
{
    const PHP_ERROR = 'php.error';
    const PHP_SHUTDOWN = 'php.shutdown';
    const PHP_EXCEPTION = 'php.exception';

    const ADMIN_OPERATE = 'admin.operate';

    const JOB_ORDER = 'job.order';

}