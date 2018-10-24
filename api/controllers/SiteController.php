<?php

namespace api\controllers;

use api\components\BaseController;
use yii\filters\VerbFilter;

/**
 * Class SiteController
 */
class SiteController extends BaseController
{

    protected $ignoreAuthAction = ['index'];

    public function behaviors()
    {
        $behaviors = parent::behaviors();

        $behaviors['verbs'] = [
            'class' => VerbFilter::class,
            'actions' => [
                'index' => ['GET'],
            ]
        ];
        return $behaviors;
    }

    public function actionIndex()
    {
        $this->_success();
    }

}
