<?php

namespace api\components;

use common\constants\ErrorCode;
use common\traits\WebControllerTrait;
use yii\web\Controller;
use Yii;
use common\models\user\UserModel;

class BaseController extends Controller
{

    use WebControllerTrait; //代码复用Trait

    /**
     * @var \yii\web\Request
     */
    protected $request;

    protected $isAuth = true; //默认需要登录认证

    //忽略登录认证的action(当控制器设为需登录认证时，可单独针对个别的action忽略登录认证)
    protected $ignoreAuthAction = [];

    //需要登录认证的action(当控制器设为忽略登录认证是，可单独针对个别的action设为需要登录认证)
    protected $requireAuthAction = [];

    protected $accessToken; //当前登录Access-Token

    protected $uid; //当前登录用户ID

    private $userInfo; //当前登录用户信息

    protected $merchantId; //当前商家ID

    protected $merchantInfo; //当前商家信息

    protected $httpType; //http请求头 http:// 或者 https://

    public function init()
    {
        parent::init();
        $this->request = Yii::$app->request;
        $this->setAllowOriginHeaders();
        $this->checkAuth();
    }

    /**
     * 检查登录态
     */
    protected function checkAuth()
    {
        $this->accessToken = (string)$this->getInput('Access-Token');

        $userModel = UserModel::instance();

        $this->uid = $userModel->checkAccessToken($this->accessToken);

        if (!$this->uid) {
            if ($this->isAuth) {
                if (!in_array($this->getAction(), $this->ignoreAuthAction)) {
                    $this->_error(ErrorCode::NOT_AUTH);
                }
            } else {
                if (in_array($this->getAction(), $this->requireAuthAction)) {
                    $this->_error(ErrorCode::NOT_AUTH);
                }
            }
        }
    }

    /**
     * 当前登录用户信息
     * @return mixed
     */
    protected function getUserInfo()
    {
        if (!$this->uid) {
            return null;
        }
        if (!$this->userInfo) {
            $this->userInfo = UserModel::instance()->getOne($this->uid);
        }
        return $this->userInfo;
    }

}
