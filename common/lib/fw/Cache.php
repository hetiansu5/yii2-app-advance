<?php
namespace common\lib\fw;

use common\lib\fw\cache\ApcuCache;
use common\lib\fw\cache\ArrayCache;
use common\lib\fw\cache\CacheInterface;
use common\lib\fw\cache\MemcacheCache;
use common\lib\fw\cache\RedisCache;

class Cache
{
    const CACHE_TYPE_ARRAY = 'array';
    const CACHE_TYPE_APCU = 'apcu';
    const CACHE_TYPE_MEMCACHE = 'memcache';
    const CACHE_TYPE_REDIS = 'redis';

    const SERIALIZE_TYPE_PHP = 'php';
    const SERIALIZE_TYPE_JSON = 'json';
    const SERIALIZE_TYPE_MSGPACK = 'msgpack';

    private function __construct()
    {
    }

    private function __clone()
    {
    }

    /**
     * @param string $cacheType
     * @param array $config
     * @param array $options
     * @return CacheInterface
     * @throws Exception
     */
    public static function getProvider($cacheType, array $config = [], array $options = [])
    {
        $cache = null;
        switch ($cacheType) {
            case self::CACHE_TYPE_ARRAY:
                $cache = ArrayCache::getInstance($config, $options);
                break;
            case self::CACHE_TYPE_APCU:
                $cache = ApcuCache::getInstance($config, $options);
                break;
            case self::CACHE_TYPE_MEMCACHE:
                $cache = MemcacheCache::getInstance($config, $options);
                break;
            case self::CACHE_TYPE_REDIS:
                $cache = RedisCache::getInstance($config, $options);
                break;
            default:
                throw new Exception('invalid cache type');
                break;
        }
        return $cache;
    }
}