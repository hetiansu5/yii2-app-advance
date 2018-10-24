<?php
namespace api\formatter\user;

use common\lib\formatter\Formatter;
use common\lib\formatter\Type;

/**
 * 用户数据结构
 */
class UserFormatter extends Formatter
{

    protected static $fields = [
        'id' => Type::INT,
        'screen_name' => Type::STRING,
        'email' => Type::STRING,
        'phone' => Type::STRING,
        'group_id' =>Type::INT,
        'balance' => Type::INT,
        'merchant_id' => Type::INT,
        'status' => Type::INT,
        'create_time' => Type::INT
    ];

}