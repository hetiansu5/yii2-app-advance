<?php
namespace backend\controllers;

use backend\components\BaseController;

/**
 * 退出
 */
class LogoutController extends BaseController
{

    protected $curModule = 'index';

    protected $isAuth = false;

    /**
     * @return string
     */
    public function actionIndex()
    {
        $this->session->destroy();
        $this->response->redirect('/login');
    }

}
