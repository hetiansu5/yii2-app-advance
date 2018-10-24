<?php
namespace common\formatter;

use common\lib\formatter\Formatter;
use common\lib\formatter\Type;

/**
 * 商品数据结构
 */
class GoodsFormatter extends Formatter
{

    protected static $fields = [
        'id' => Type::INT,
        'name' => Type::STRING,
        'num' => Type::INT,
        'price' => Type::INT,
    ];

}