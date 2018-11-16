<?php
namespace common\lib\fw;

class Logger
{
    use InstanceTrait;

    const LEVEL_ERROR = 1;
    const LEVEL_WARN = 2;
    const LEVEL_INFO = 3;
    const LEVEL_DEBUG = 4;

    const LEVEL_ERROR_TEXT = 'error';
    const LEVEL_WARN_TEXT = 'warn';
    const LEVEL_INFO_TEXT = 'info';
    const LEVEL_DEBUG_TEXT = 'debug';

    private $levelArr = [
        self::LEVEL_ERROR => self::LEVEL_ERROR_TEXT,
        self::LEVEL_WARN => self::LEVEL_WARN_TEXT,
        self::LEVEL_INFO => self::LEVEL_INFO_TEXT,
        self::LEVEL_DEBUG => self::LEVEL_DEBUG_TEXT,
    ];

    const HANDLER_FILE = 'file';
    const HANDLER_STDOUT = 'stdout';

    /**
     * @param $level
     * @param $message array|string
     * @param $type
     * @return bool
     */
    public function log($level, $message, $type)
    {
        //app.log_handler为file时，日志格式为:[time(ISO8601)]  [host]  [type(service.module.function)]  [req_id]  [server_ip]  [client_ip]  [message(json:code,message,file,line-template,trace,biz_data)]
        //app.log_handler为stdout时，日志格式为:{"t": "time(ISO8601)", "lvl": "level", "h": "host", "type": "type(service.module.function)", "reqid": "req_id", "sip": "server_ip", "cip": "client_ip", "msg": {"code": 0, "message": "xxx", "file": "file", "line-template": 0}}

        $logLevelText = \Yii::$app->params['log.level'];
        $foundKey = array_search($logLevelText, $this->levelArr);
        if ($foundKey === false) {
            $logLevel = self::LEVEL_INFO;
        } else {
            $logLevel = $foundKey;
        }
        //大于配置中的log_level则不打印,默认打印error,warn,info类型
        if ($level > $logLevel) {
            return false;
        }

        $host = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : 'cli';
        $time = date('c'); //ISO8601标准时间格式,形如:2016-11-02T06:46:10+00:00
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

        $logHandler = \Yii::$app->params['log.handler'];
        $result = false;
        $levelText = isset($this->levelArr[$level]) ? $this->levelArr[$level] : self::LEVEL_INFO_TEXT;

        $isString = is_string($message);
        if (!$isString) {
            $message = json_encode($message, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES|JSON_PARTIAL_OUTPUT_ON_ERROR);
        }

        switch ($logHandler) {
            case self::HANDLER_STDOUT:
                $log = [
                    't' => $time,
                    'lvl' => $levelText,
                    'h' => $host,
                    'type' => $type,
                    'cip' => $clientIp,
                    'path' => $pathInfo,
                    'msg' => $message
                ];
                $content = json_encode($log, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES|JSON_PARTIAL_OUTPUT_ON_ERROR) . "\n";
                $logStdout = \Yii::$app->params['log.stdout'];
                if ($logStdout) {
                    $fp = fopen($logStdout, 'wb');
                    $result = fwrite($fp, $content) !== false;
                    fclose($fp);
                } else {
                    $fp = defined('STDOUT') ? STDOUT : fopen('php://stdout', 'wb');
                    $result = fwrite($fp, $content) !== false;
                }

                break;

            case self::HANDLER_FILE:
                $log = [
                    't' => $time,
                    'level' => $levelText,
                    'h' => $host,
                    'type' => $type,
                    'cip' => $clientIp,
                    'path' => $pathInfo,
                    'msg' => $message
                ];
                $content = json_encode($log, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES|JSON_PARTIAL_OUTPUT_ON_ERROR) . "\n";
                $dir = \Yii::$app->params['log.path'];
                if (!$dir) {
                    break;
                }
                if (!is_dir($dir)) {
                    mkdir($dir, 0777, true);
                }
                $filename = $dir . '/' . $levelText . '.log';
                $isFileExist = is_file($filename);
                $result = error_log($content, 3, $filename);
                if ($result && !$isFileExist) {
                    chmod($filename, 0777);
                }
                break;

            default:
                if ($isString) {
                    $message = str_replace(["\r", "\n"], ' ', $message);
                }
                $log = [
                    $time,
                    $host,
                    $type,
                    $clientIp,
                    $pathInfo,
                    $message
                ];
                $content = '[' . implode(']  [', $log) . ']' . "\n";
                $dir = \Yii::$app->params['log.path'];
                if (!$dir) {
                    break;
                }
                if (!is_dir($dir)) {
                    mkdir($dir, 0777, true);
                }
                $filename = $dir . '/' . $levelText . '.log';
                $isFileExist = is_file($filename);
                $result = error_log($content, 3, $filename);
                if ($result && !$isFileExist) {
                    chmod($filename, 0777);
                }
                break;
        }
        return $result;
    }

    public function error($logInfo, $type)
    {
        $this->log(self::LEVEL_ERROR, $logInfo, $type);
    }

    public function warn($logInfo, $type)
    {
        $this->log(self::LEVEL_WARN, $logInfo, $type);
    }

    public function info($logInfo, $type)
    {
        $this->log(self::LEVEL_INFO, $logInfo, $type);
    }

    public function debug($message, $type)
    {
        $this->log(self::LEVEL_DEBUG, $message, $type);
    }
}