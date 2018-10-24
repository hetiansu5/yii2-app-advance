<?php
namespace common\lib\fw\exception;

use common\lib\fw\Exception;

class MemcacheException extends Exception
{
    public $logType = '';
    public $method = '';
    public $params = [];
    public $config = null;
}