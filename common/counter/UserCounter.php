<?php
namespace common\counter;

use common\lib\fw\Counter;
use common\lib\fw\CounterTrait;

/**
 * 用户相关
 * 长度14位（前缀2位+业务id10位+子业务2位）
 * @author hts
 */
class UserCounter
{
    use CounterTrait;

    const DELIVERY_ADDRESS_COUNT = 'd_a_count'; //发货地址数
    const RECEIVE_ADDRESS_COUNT = 'r_a_count';  //收货地址数

    private function __construct()
    {
        $this->counterType = Counter::COUNTER_TYPE_REDISCOUNTER;
        $this->counterConfig = \Yii::$app->params['counter.main'];
        $this->prefixKey = CounterKey::USER;
        $this->fields = [ //子业务
            self::DELIVERY_ADDRESS_COUNT,
            self::RECEIVE_ADDRESS_COUNT,
        ];
        $this->fieldsAbbreviation = [ //子业务键名存储时简化映射
            self::DELIVERY_ADDRESS_COUNT => 'd',
            self::RECEIVE_ADDRESS_COUNT => 'r',
        ];
    }

}