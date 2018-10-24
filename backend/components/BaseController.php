<?php

namespace backend\components;

use common\constants\LogType;
use common\lib\fw\Logger;
use common\lib\fw\Purview;
use common\models\account\AccountModel;
use common\models\account\AccountRoleModel;
use common\traits\ControllerTrait;
use yii\web\Controller;
use \Yii;

class BaseController extends Controller
{

    use ControllerTrait;

    /**
     * @var \yii\web\Request
     */
    protected $request;

    /**
     * @var \yii\web\View
     */
    protected $view;

    /**
     * @var \yii\web\Response
     */
    protected $response;

    /**
     * @var \yii\web\Session
     */
    protected $session;


    protected $curModule; //当期访问控制器所属模块，每个控制器需要赋初值
    protected $curController; //当期访问的controller名称
    protected $curAction; //当期访问的action名称

    protected $isAuth = true; //是否要求登录

    //忽略登录认证的action(当控制器设为需登录认证时，可单独针对个别的action忽略登录认证)
    protected $ignoreAuthAction = [];

    //需要登录认证的action(当控制器设为忽略登录认证是，可单独针对个别的action设为需要登录认证)
    protected $requireAuthAction = [];

    protected $adminId; //账号ID
    protected $adminInfo; //登录账号信息

    private $renderParams = []; //渲染到布局的参数数组

    private static $menus = [];

    public function init()
    {
        parent::init();
        $this->_initMCA();
        $this->_initParams();
        $this->_checkAccess();
    }

    /**
     * 初始化参数
     */
    private function _initParams()
    {
        $this->request = Yii::$app->request;
        $this->response = Yii::$app->response;
        $this->session = Yii::$app->session;
        $this->view = Yii::$app->view;

        $this->adminId = (int)$this->session->get('current_account_id');

        if ($this->adminId > 0) {
            $accountModel = AccountModel::instance();
            $this->adminInfo = $accountModel->getOne($this->adminId);
            unset($this->adminInfo['password']);

            //账号信息
            $this->_assign('adminInfo', $this->adminInfo);
        }
    }

    /**
     * 初始化当前控制器和action参数
     */
    private function _initMCA()
    {
        $parseInfo = parse_url($_SERVER['REQUEST_URI']);
        $arr = array_values(array_filter(explode('/', $parseInfo['path'])));
        $this->curController = $this->id;
        $this->curAction = $arr[1] ?: 'index';
    }

    /**
     * @param null $key
     * @return array|mixed
     */
    protected function getRenderParams($key = null)
    {
        return isset($key) ? $this->renderParams[$key] : $this->renderParams;
    }

    /**
     * @param $key
     * @param $data
     * @return $this
     */
    protected function _assign($key, $data)
    {
        $this->renderParams[$key] = $data;
        return $this;
    }

    /**
     * 赋值模板全局变量
     */
    private function _assignGlobalViewParams()
    {
        $this->view->params['curModule'] = $this->curModule;
        $this->view->params['curController'] = $this->curController;
        $this->view->params['curAction'] = $this->curAction;

        //账号信息
        $this->view->params['adminInfo'] = $this->adminInfo;

        //导航、菜单
        $menus = $this->getMenus();
        $this->view->params['menus'] = $menus;
    }

    /**
     * 渲染一个 视图名 并使用一个 布局 返回到渲染结果
     * @param null $view
     * @return string
     */
    protected function _render($view = null)
    {
        $this->_assignGlobalViewParams();
        $view = $view ?: $this->curAction;
        return $this->render($view, $this->getRenderParams());
    }

    /**
     * 渲染一个 视图名 并且不使用布局
     * @param null $view
     * @return string
     */
    protected function _renderPartial($view = null)
    {
        $view = $view ?: $this->curAction;
        return $this->renderPartial($view, $this->getRenderParams());
    }

    /**
     * 检查账号权限
     */
    private function _checkAccess()
    {
        //登录、访问权限
        if (!$this->adminId) {
            if ($this->isAuth) {
                if (in_array($this->curAction, $this->ignoreAuthAction)) {
                    return;
                }
            } else {
                if (!in_array($this->curAction, $this->requireAuthAction)) {
                    return;
                }
            }
        }

        if (!$this->adminId) {
            if ($this->request->isAjax) {
                $this->ajaxError('未登录');
            } else {
                $this->response->redirect('/login');
            }
        }

        //root账号为最高权限账号,不受权限限制
        $accountModel = AccountModel::instance();
        if (!$accountModel->isManager($this->adminInfo)) {
            $purview = Purview::getInstance();
            $isReadonly = !empty($this->adminInfo['is_readonly']);
            $privileges = $this->getPrivileges($this->adminId);
            $isGranted = $purview->isGranted(
                $this->curModule,
                $this->curController,
                $this->curAction,
                $privileges,
                $isReadonly
            );
            if (!$isGranted) {
                if ($this->request->isAjax) {
                    if ($_REQUEST('is_modal') == 1) {
                        echo '<div class="row" style="text-align:center;font-weight:bolder;color:#f00;">权限不足</div>';
                        Yii::$app->end();
                    } else {
                        $this->ajaxError('权限不足');
                    }
                } else {
                    $this->ajaxError('权限不足');
                }
            }
        }
    }

    protected function getPrivileges($acctId)
    {
        $purview = Purview::getInstance();
        $accountRoleModel = AccountRoleModel::instance();
        $roleList = $accountRoleModel->getRoleListByAccountId($acctId);
        $privilegesArr = array_column($roleList, 'nodes');
        return $purview->mergePrivileges($privilegesArr);
    }

    /**
     * 左侧菜单
     * @return array
     */
    protected function getMenus()
    {
        $purview = Purview::getInstance();
        $accountModel = AccountModel::instance();
        if (!self::$menus) {
            $privileges = $accountModel->isManager($this->adminInfo) ? '*' : $this->getPrivileges($this->adminId);
            self::$menus = $purview->getMenus($privileges);
        }
        return self::$menus;
    }

    /**
     * @param $code
     * @param string $msg
     * @param array $data
     */
    private function ajaxOutput($code, $msg = '', $data = [])
    {
        if (!$msg) {
            $msg = $code == 0 ? '操作成功' : '操作失败';
        }
        //Yii的头设置模式
        $rep = Yii::$app->response;
        $rep->format = $rep::FORMAT_RAW;
        if ($this->request->isAjax) {
            $data['code'] = (int)$code;
            $data['msg'] = (string)$msg;
            $rep->headers->set('Content-Type', 'application/json');
            $rep->data = json_encode($data, JSON_PARTIAL_OUTPUT_ON_ERROR);
        } else {
            $rep->data = $msg;
        }
        $rep->send();
        Yii::$app->end();
    }

    /**
     * @param array $data
     * @param string $msg
     */
    protected function ajaxSuccess($data = [], $msg = '')
    {
        $this->ajaxOutput(0, $msg, $data);
    }

    /**
     * @param string $msg
     * @param array $data
     */
    protected function ajaxError($msg = '', $data = [])
    {
        $this->ajaxOutput(1, $msg, $data);
    }

    /**
     * 简要的操作日志
     * @param string $msg
     */
    protected function addAdminLog($msg)
    {
        $arr = array(
            'email' => $this->adminInfo['email'],
            'message' => $msg
        );
        Logger::getInstance()->info($arr, LogType::ADMIN_OPERATE);
    }

}
