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

    public function init()
    {
        parent::init();
        $this->request = Yii::$app->request;
    }

}
