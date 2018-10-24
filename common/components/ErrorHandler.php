<?php

namespace common\components;

use common\constants\ErrorCode;
use common\constants\LogType;
use common\lib\fw\Logger;
use common\traits\WebControllerTrait;
use yii\web\MethodNotAllowedHttpException;
use yii\web\NotFoundHttpException;

/**
 * 自定义的错误处理类
 * @author hts
 */
class ErrorHandler extends \yii\web\ErrorHandler
{

    use WebControllerTrait;

    protected function renderException($exception)
    {
        if ($exception instanceof NotFoundHttpException) {
            $this->_error(ErrorCode::NOT_FOUND);
        } else if ($exception instanceof MethodNotAllowedHttpException) {
            $this->_error(ErrorCode::NOT_ALLOWED_METHOD);
        }

        $data = $this->convertExceptionToArray($exception);
        $this->setAllowOriginHeaders();
        Logger::getInstance()->error($data, LogType::PHP_EXCEPTION);
        $this->_error(ErrorCode::SYSTEM_ERROR, null, ['exception' => $data]);
    }

}
