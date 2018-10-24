<?php
namespace common\formatter;

use common\lib\formatter\CustomType;

/**
 * 自定义map结构
 * @author hts
 */
class MapType implements CustomType
{

    public static function item($item)
    {
        if (is_array($item)) {
            return $item;
        }
        $item = json_decode($item, true);
        if (is_array($item)) {
            return $item;
        }
        return new \StdClass();
    }

}