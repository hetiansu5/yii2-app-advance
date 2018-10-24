<?php
namespace common\traits;

use common\constants\ErrorCode;
use \Yii;

/**
 * 面向用户的控制器代码复用类
 * @author hts
 */
Trait WebControllerTrait
{

    use ControllerTrait;

    /**
     * 获取多语言
     * @remark 语言包配置目录 common/messages/{language}/{category}.php
     * @param $errorCode
     * @param $category
     * @return string
     */
    protected function getLangMessage($errorCode, $category = 'error')
    {
        return Yii::t($category, $errorCode);
    }

    /**
     * API失败返回
     * @param $errorCode
     * @param null $error
     * @param array $response
     */
    protected function _error($errorCode, $error = null, $response = [])
    {
        $msg = $this->getLangMessage($errorCode);
        if (empty($msg)) {
            $msg = $error;
        }
        $this->_output($errorCode, $msg, $error, $response);
    }

    /**
     * API成功返回
     * @param array $response
     */
    protected function _success($response = [])
    {
        $this->_output(ErrorCode::SUCCESS, 'success', '', $response);
    }

    /**
     * 输出
     * @param $errorCode
     * @param $msg
     * @param $error
     * @param array $response
     */
    protected function _output($errorCode, $msg, $error, $response = [])
    {
        ob_clean();
        //Yii框架的响应设置模式
        $rep = Yii::$app->response;
        $rep->statusCode = 200;
        $rep->format = $rep::FORMAT_RAW;
        $rep->headers->set('Content-Type', 'application/json');

        $result = [
            'meta' => [
                'code' => (int)$errorCode, //错误码
                'msg' => (string)$msg, //用户端显示的错误信息
                'error' => (string)$error, //开发者查看的错误信息
                'request_uri' => Yii::$app->request->getPathInfo(), //请求路径
            ],
            'response' => $response ? $response : new \stdClass() //响应信息主体
        ];

        $rep->data = json_encode($result, JSON_PARTIAL_OUTPUT_ON_ERROR);
        $rep->send();
        Yii::$app->end();
    }

    /**
     * 跨域响应头
     */
    protected function setAllowOriginHeaders()
    {
        $rep = Yii::$app->response;
        $rep->headers->set('Access-Control-Allow-Headers', 'Content-Type,X-Requested-With,Access-Token,token');
        $rep->headers->set('Access-Control-Allow-Origin', '*');
        $requestType = Yii::$app->request->getMethod();
        if ($requestType == 'OPTIONS') {
            //指定预检请求的有效期，单位为秒
            $rep->headers->set('Access-Control-Max-Age', 30 * 86400);
            $this->_error(ErrorCode::NOT_ALLOWED_METHOD);
        }
    }

    /**
     * @return mixed
     */
    protected function getAction()
    {
        static $action;
        if (!isset($action)) {
            $parseInfo = parse_url($_SERVER['REQUEST_URI']);
            $arr = array_values(array_filter(explode('/', $parseInfo['path'])));
            $action = $arr[1] ?: 'index';
        }
        return $action;
    }

}