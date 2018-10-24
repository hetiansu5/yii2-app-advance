<?php
namespace common\traits;

use common\constants\LogType;
use common\lib\fw\Logger;
use yii\base\ErrorException;
use yii\base\Exception;
use yii\base\UserException;
use yii\web\HttpException;

/**
 * Exception复用类
 * @author hts
 */
Trait ExceptionTrait
{

    /**
     * 将异常转换数组信息
     * @param \Exception|\Error $exception
     * @return array
     */
    protected function convertExceptionToArray($exception)
    {
        $array = [
            'name' => ($exception instanceof Exception || $exception instanceof ErrorException) ? $exception->getName() : 'Exception',
            'message' => $exception->getMessage(),
            'code' => $exception->getCode(),
        ];
        if ($exception instanceof HttpException) {
            $array['status'] = $exception->statusCode;
        }
        if (YII_DEBUG) {
            $array['type'] = get_class($exception);
            if (!$exception instanceof UserException) {
                $array['file'] = $exception->getFile();
                $array['line'] = $exception->getLine();
                $array['stack-trace'] = explode("\n", $exception->getTraceAsString());
                if ($exception instanceof \yii\db\Exception) {
                    $array['error-info'] = $exception->errorInfo;
                }
            }
        }
        if (($prev = $exception->getPrevious()) !== null) {
            $array['previous'] = $this->convertExceptionToArray($prev);
        }

        return $array;
    }

    /**
     * @param \Exception|\Error $exception
     */
    protected function handleException($exception)
    {
        $msg = $this->convertExceptionToArray($exception);
        Logger::getInstance()->error($msg, LogType::PHP_EXCEPTION);
    }

}