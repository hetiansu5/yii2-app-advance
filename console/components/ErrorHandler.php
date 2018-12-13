<?php

namespace console\components;

use common\constants\LogType;
use common\lib\fw\Logger;
use yii\base\ErrorException;
use yii\base\Exception;
use yii\base\UserException;

/**
 * 自定义的错误处理类
 * @author hts
 */
class ErrorHandler extends \yii\console\ErrorHandler
{

    protected function renderException($exception)
    {
        $data = $this->convertExceptionToArray($exception);
        Logger::getInstance()->error($data, LogType::PHP_EXCEPTION);
        parent::renderException($exception);
    }

    /**
     * Converts an exception into an array.
     * @param \Exception|\Error $exception the exception being converted
     * @return array the array representation of the exception.
     */
    protected function convertExceptionToArray($exception)
    {
        $array = [
            'name' => ($exception instanceof Exception || $exception instanceof ErrorException) ? $exception->getName() : 'Exception',
            'message' => $exception->getMessage(),
            'code' => $exception->getCode(),
        ];

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

}
