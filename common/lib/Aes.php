<?php
namespace common\lib;

/**
 * AES加解密
 * @author hts
 */
class Aes
{
    const KEY = "af520ro9149e061d"; //秘钥
    const IV = "5ept3f606mi20312"; //加密的初始向量

    /**
     * 加密
     * @param string $algorithm 加密算法
     * @param $str
     * @param string $iv
     * @param string $key
     * @return string
     */
    public static function encrypt($algorithm, $str, $iv = self::IV, $key = self::KEY)
    {
        $iv = self::strPadOrCut($iv, 16, "\0");
        $key = self::strPadOrCut($key, 16, "\0");
        $encrypted = openssl_encrypt($str, $algorithm, $key, OPENSSL_RAW_DATA, $iv);
        return base64_encode($encrypted);
    }

    /**
     * 解密
     * @param string $algorithm 加密算法
     * @param $data
     * @param string $iv
     * @param string $key
     * @return string
     */
    public static function decrypt($algorithm, $data, $iv = self::IV, $key = self::KEY)
    {
        $iv = self::strPadOrCut($iv, 16, "\0");
        $key = self::strPadOrCut($key, 16, "\0");
        $encrypted = base64_decode($data);
        $decrypted = openssl_decrypt($encrypted, $algorithm, $key, OPENSSL_RAW_DATA, $iv);
        return $decrypted;
    }


    /**
     * aes-256-cbc加密
     * @param $str
     * @param string $iv
     * @param string $key
     * @return string
     */
    public static function aes256cbcEncrypt($str, $iv = self::IV, $key = self::KEY)
    {
        $iv = self::strPadOrCut($iv, 16, "\0");
        $key = self::strPadOrCut($key, 16, "\0");
        return self::encrypt('aes-256-cbc', $str, $iv, $key);
    }

    /**
     * aes-256-cbc解密
     * @param $data
     * @param string $iv
     * @param string $key
     * @return string
     */
    public static function aes256cbcDecrypt($data, $iv = self::IV, $key = self::KEY)
    {
        $iv = self::strPadOrCut($iv, 16, "\0");
        $key = self::strPadOrCut($key, 16, "\0");
        return self::decrypt('aes-256-cbc', $data, $iv, $key);
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