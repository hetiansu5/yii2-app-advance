<?php
namespace common\counter;

use common\constants\Common;

/**
 * 计数器前缀键名
 * @author hts
 */
class CounterKey
{
    const ID = Common::PROJECT_KEY . 'i:';
    const USER = Common::PROJECT_KEY . 'e:';
}