<?php
namespace common\traits;

use common\constants\Common;
use \Yii;

/**
 * 控制器代码复用类
 * @author hts
 */
Trait ControllerTrait
{

    /**
     * 当前分页
     * @return int
     */
    protected function getPage()
    {
        $page = (int)$this->getInput('page');
        return $page < 1 ? 1 : $page;
    }

    /**
     * 分页条目数
     * @param int $default
     * @param int $max
     * @return int
     */
    protected function getLimit($default = Common::DEFAULT_PAGE_LIMIT, $max = Common::DEFAULT_PAGE_LIMIT_MAX)
    {
        $limit = (int)$this->getInput('limit');
        return $limit >= 1 ? ($limit > $max ? $max : $limit) : $default;
    }

    /**
     * 批量获取请求参数
     * @param array $fields
     * @param string|null $method
     * @return array
     */
    protected function getBatchInputData($fields, $method = null)
    {
        $data = [];
        $method = strtoupper($method);
        foreach ($fields as $fd) {
            if ($method == 'P') {
                $data[$fd] = Yii::$app->request->post($fd);
            } else if ($method == 'G') {
                $data[$fd] = Yii::$app->request->get($fd);
            } else {
                $data[$fd] = $this->getInput($fd);
            }
        }
        return $data;
    }

    /**
     * @param $field
     * @return mixed
     */
    protected function getInput($field)
    {
        return isset($_REQUEST[$field]) ? $_REQUEST[$field] : null;
    }

}