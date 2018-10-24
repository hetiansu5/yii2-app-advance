<?php
namespace common\lib\fw\exception;

use common\lib\fw\Exception;

class RedisException extends Exception
{
    public $logType = '';
    public $method = '';
    public $params = [];
    public $config = null;
    public $rwType = null;
}