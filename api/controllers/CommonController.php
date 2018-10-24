<?php

namespace api\controllers;

use common\service\aws\AwsService;
use api\components\BaseController;
use yii\filters\VerbFilter;
use \Yii;

/**
 * 公共模块
 */
class CommonController extends BaseController
{

    protected $isAuth = false;

    public function behaviors()
    {
        $behaviors = parent::behaviors();

        $behaviors['verbs'] = [
            'class' => VerbFilter::class,
            'actions' => [
                'top-up-config' => ['GET'],
            ]
        ];
        return $behaviors;
    }

    /**
     * 充值金额配置
     */
    public function actionTopUpConfig()
    {
        $this->_success([
            'data' => [
                'amount' => 1000
            ]
        ]);
    }

}
