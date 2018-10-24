<?php

namespace console\controllers;

use yii\console\Controller;

class TestController extends Controller
{

    public $times;

    /**
     * 允许传入的类属性
     * @param string $actionID
     * @return array|\string[]
     */
    public function options($actionID)
    {
        $options = parent::options($actionID);
        // $actionId might be used in subclasses to provide options specific to action id
        $options[] = "times";
        return $options;
    }

    /**
     * Displays homepage.
     *
     */
    public function actionSay($word)
    {
        var_dump($word, $this->times);
    }

}
