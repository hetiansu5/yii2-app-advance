<?php
namespace common\lib;

/**
 * 字符串处理函数库
 * @author hts
 */
class Strings
{

    const ENCODE_UTF8 = 'utf-8';

    /**
     * 计算字符数
     * @return int
     */
    public static function mbStrLen($str)
    {
        return mb_strlen($str, self::ENCODE_UTF8);
    }

    /**
     * 按照字符单位进行切割
     * @param string $str
     * @param int $len
     * @param int $start
     * @return string
     */
    public static function mbCut($str, $len, $start = 0)
    {
        return mb_substr($str, $start, $len, self::ENCODE_UTF8);
    }

    /**
     * 字符串限制长度，不足补0，过长切割
     * @param string $str
     * @param int $limit 限制长度
     * @param string $char
     * @return string
     */
    public static function strPadOrCut($str, $limit, $char = '0')
    {
        $len = strlen($str);
        if ($len > $limit) {
            $str = substr($str, -$limit);
        } else if ($len < $limit) {
            $str = str_pad($str, $limit, $char, STR_PAD_LEFT);
        }
        return $str;
    }

}