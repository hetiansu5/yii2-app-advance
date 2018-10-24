<?php
namespace common\lib;

/**
 * 签名
 * @author hts
 */
class Signature
{

    const SECRET = 'Po03e&MyLink#$Weo02%';

    /**
     * 获取传入数据的签名值
     * @param $data
     * @param null|string $secret 盐值
     * @return string
     */
    public static function get($data, $secret = null)
    {
        $data = json_encode($data);
        if (!$secret) {
            $secret = self::SECRET;
        }
        $str = mt_rand(1000, 99999) . microtime(true) . $data . $secret;
        return substr(sha1($str), 0, 32);
    }

    /**
     * 检查签名一致性
     * @param $data
     * @param $sig
     * @param null $secret
     * @return bool
     */
    public static function check($data, $sig, $secret = null)
    {
        return self::get($data, $secret) === $sig;
    }

}