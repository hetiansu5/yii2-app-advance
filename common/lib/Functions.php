<?php
namespace common\lib;

use common\constants\Common;

/**
 * 常用函数库
 * @author hts
 */
class Functions
{

    /**
     * 分页数据结构
     * @param $page
     * @param $limit
     * @param $total
     * @return array
     */
    public static function getPaging($page, $limit, $total)
    {
        return [
            'page' => (int)$page,
            'limit' => (int)$limit,
            'total' => (int)$total,
        ];
    }

    /**
     * 两个数组指定参数值是否相同
     * @param array $oldMap
     * @param array $newMap
     * @return bool
     */
    public static function isSame($oldMap, $newMap, $fields = [])
    {
        if (!$fields) {
            $fields = array_keys($newMap);
        }

        foreach ($fields as $field) {
            if ($oldMap[$field] != $newMap[$field]) {
                return false;
            }
        }
        return true;
    }

    /**
     * 将索引数组转换为关联数字
     * @param array $list
     * @param string $field
     * @return array
     */
    public static function listToMap($list, $field)
    {
        $newList = [];
        foreach ($list as $val) {
            if (isset($val[$field])) {
                $newList[$val[$field]] = $val;
            }
        }
        return $newList;
    }

    /**
     * @param $where
     * @param $input
     * @param $fields
     * @param $type
     * @param null $operate
     */
    public static function setWhere(&$where, &$input, $fields, $type, $operate = null)
    {
        foreach ($fields as $field) {
            if (Validate::isEmptyString($input[$field])) {
                continue;
            }
            switch ($type) {
                case Common::TYPE_INT:
                    $item = (int)$input[$field];
                    break;
                case Common::TYPE_FLOAT:
                    $item = (float)$input[$field];
                    break;
                default :
                    $item = (string)$input[$field];
                    break;
            }
            $key = $operate ? $field . ' ' . $operate : $field;
            $where[$key] = $item;
        }
    }

    /**
     * 针对中文不unicode
     * @param $arr
     * @return string
     */
    public static function jsonEncode($arr)
    {
        return json_encode($arr, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PARTIAL_OUTPUT_ON_ERROR);
    }

    /**
     * 转换为字符串(kibana需要对每个层级的数据结构要确认的，不一致会导入无法写入)
     * @param $data
     * @return string
     */
    public static function toString($data)
    {
        if (is_array($data) || is_object($data)) {
            return self::jsonEncode($data);
        } else {
            return (string)$data;
        }
    }

    /**
     * XSS攻击防御过滤
     * @param mixed $data
     */
    public static function outputFilter(&$data)
    {
        if (is_array($data)) {
            foreach ($data as $key => $val) {
                self::outputFilter($data[$key]);
            }
        } else if (is_object($data)) {
            foreach ($data as $key => $val) {
                self::outputFilter($data->{$key});
            }
        } else if (is_string($data)) {
            $data = htmlspecialchars($data);
        }
    }

    /**
     * 获取文件后缀名
     * @param string $file
     * @return string
     */
    public static function getFileExt($file)
    {
        return strtolower(substr(strrchr($file, '.'), 1));
    }

    /**
     * 格式化字节大小
     * @param int $size 字节数
     * @param string $delimiter 数字和单位分隔符
     * @return string  格式化后的带单位的大小
     */
    public static function formatBytes($size, $delimiter = '')
    {
        $units = array('B', 'KB', 'MB', 'GB', 'TB', 'PB');
        for ($i = 0; $size >= 1024 && $i < 5; $i++)
            $size /= 1024;
        return round($size, 2) . $delimiter . $units[$i];
    }

    /**
     * 获取随机hash值
     * @param int $len
     * @return string|bool
     */
    public static function getRandomHash($len)
    {
        $len = (int)$len;
        if ($len <= 0) {
            return false;
        }
        return substr(md5('lg_' . uniqid() . mt_rand(1, 999999)), 0, $len);
    }

    /**
     * 获取邮箱配置
     * @param $data
     * @return array
     */
    public static function getMailConfig($data)
    {
        $mailer_config = [
            'class' => 'Swift_SmtpTransport',
            'host' => $data['host'],
            'username' => $data['email'],
            'password' => $data['pwd'],
            'port' => $data['port'],
        ];
        if ($data['port'] == 465) {
            $mailer_config['encryption'] = "ssl";
        } elseif ($data['port'] == 587) {
            $mailer_config['encryption'] = "tls";
        }
        return $mailer_config;
    }

}