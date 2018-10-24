<?php
namespace backend\controllers;

use backend\components\BaseController;
use common\models\account\AccountModel;
use Gregwar\Captcha\CaptchaBuilder;
use Gregwar\Captcha\PhraseBuilder;
use yii\web\Response;

/**
 * 登录页
 */
class LoginController extends BaseController
{

    protected $curModule = 'index';

    protected $isAuth = false;

    /**
     * @return string
     */
    public function actionIndex()
    {
        return $this->_renderPartial('index');
    }

    /**
     * 验证码
     */
    public function actionVerifyCode()
    {
        //Yii的头设置模式
        $this->response->format = Response::FORMAT_RAW;
        $this->response->headers->set('Content-Type', 'image/jpeg');

        $phraseBuilder = new PhraseBuilder(4);
        $builder = new CaptchaBuilder(null, $phraseBuilder);
        $builder->build();

        $code = $builder->getPhrase();
        $this->session->set('verify_code', $code);

        $builder->output();
    }

    /**
     * 登录认证
     */
    public function actionAuth()
    {
        $account = (string)$this->request->post('username');
        $password = (string)$this->request->post('password');
        $verifyCode = (string)$this->request->post('verify_code');

        if (!$account) {
            $this->ajaxError('账号不能为空');
        } else if (!$password) {
            $this->ajaxError('密码不能为空');
        } else if (!$verifyCode) {
            $this->ajaxError('请输入验证码');
        }

        $sessionVerifyCode = (string)$this->session->get('verify_code');
        if (!$sessionVerifyCode || $verifyCode !== $sessionVerifyCode) {
            $this->_resetVerifyCode();
            $this->ajaxError('验证码错误');
        }

        $model = AccountModel::instance();
        $info = $model->getOneByEmail($account);
        if (!$info) {
            $this->_resetVerifyCode();
            $this->ajaxError('账号或者密码错误1');
        } else if ($info['status'] != $model::STATUS_NORMAL) {
            $this->_resetVerifyCode();
            $this->ajaxError('账号异常');
        } else if (!$model->checkPassword($password, $info['password'])) {
            $this->_resetVerifyCode();
            $this->ajaxError('账号或者密码错误2');
        }

        $this->_resetVerifyCode();
        $this->session->set('current_account_id', $info['id']);
        $this->ajaxSuccess();
    }

    /**
     * 重置认证码
     */
    private function _resetVerifyCode()
    {
        $this->session->set('verify_code', '');
    }

}
