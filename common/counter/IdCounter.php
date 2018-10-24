<?php
namespace common\counter;

use common\lib\fw\Counter;
use common\lib\fw\CounterTrait;
use common\lib\Strings;

/**
 * ID自增计数器
 * @author hts
 */
class IdCounter
{

    const TYPE_LOGISTICS = 1;
    const TYPE_SHOP = 2;

    const INIT_PLUS = 1019283746;
    const STEP_PLUS = 2000000000;

    use CounterTrait;

    private function __construct()
    {
        $this->counterType = Counter::COUNTER_TYPE_REDISCOUNTER;
        $this->counterConfig = \Yii::$app->params['counter.main'];
        $this->prefixKey = CounterKey::ID;
    }

    /**
     * 获取订单ID
     * @param int|null $createTime
     * @param int $type 1-物流订单  2-商城订单
     * @return int
     */
    public function getOrderId($createTime = null, $type = self::TYPE_LOGISTICS)
    {
        $key = 'order' . $type;
        $id = $this->incr($key);
        if ($id === false) {
            $id = mt_rand(1, 999);
        }

        if (!$createTime) {
            $createTime = time();
        }

        $timeArr = explode(' ', microtime());
        $secondUnique = Strings::strPadOrCut($id + substr($createTime * 3, -3), 3)
            . Strings::strPadOrCut(intval($timeArr[0] * 100), 2);

        $plus = self::INIT_PLUS;
        if (self::TYPE_SHOP == $type) {
            $plus += self::STEP_PLUS;
        }

        return ($createTime + $plus) . $secondUnique;
    }


}