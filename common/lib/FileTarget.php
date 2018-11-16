<?php
namespace common\lib;

use yii\helpers\VarDumper;
use yii\log\Logger;

/**
 * 系统日志输出重写
 * @author hts
 */
class FileTarget extends \yii\log\FileTarget
{

    /**
     * Formats a log message for display as a string.
     * @param array $message the log message to be formatted.
     * The message structure follows that in [[Logger::messages]].
     * @return string the formatted message
     */
    public function formatMessage($message)
    {
        list($text, $level, $category, $timestamp) = $message;
        $level = Logger::getLevelName($level);
        if (!is_string($text)) {
            // exceptions may not be serializable if in the call stack somewhere is a Closure
            if ($text instanceof \Throwable || $text instanceof \Exception) {
                $text = (string) $text;
            } else {
                $text = VarDumper::export($text);
            }
        }
        $traces = [];
        if (isset($message[4])) {
            foreach ($message[4] as $trace) {
                $traces[] = "in {$trace['file']}:{$trace['line']}";
            }
        }

        $host = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : 'cli';
        $request = \Yii::$app->request;
        if (method_exists($request, 'getUserIp')) {
            $clientIp = $request->getUserIP();
        } else {
            $clientIp = '';
        }
        $pathInfo = '';
        if (method_exists($request, 'getPathInfo')) {
            $pathInfo = $request->getPathInfo();
        }

        $arr = [
            't' => date('c', $timestamp),
            'lvl' => $level,
            'h' => $host,
            'cate' => $category,
            'cip' => $clientIp,
            'action' => $pathInfo,
            'msg' => $text,
            'traces' => implode(',', $traces)
        ];
        return json_encode($arr, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES|JSON_PARTIAL_OUTPUT_ON_ERROR);
    }

}
