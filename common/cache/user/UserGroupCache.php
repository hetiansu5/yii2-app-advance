<?php
namespace common\cache\user;

use common\lib\fw\Cache;
use common\cache\CacheTrait;
use common\cache\UserModuleKey;

/**
 * 用户组缓存
 * @author hts
 */
class UserGroupCache
{
    use CacheTrait;



    private function __construct()
    {
        $this->ttl = 86400; //缓存周期
        if (YII_ENV_DEV) { //因为window本地开发用不了memcached扩展
            $this->cacheType = Cache::CACHE_TYPE_REDIS; //使用redis缓存
            $this->cacheConfig = \Yii::$app->params['redis.main']; //缓存服务器配置
            $this->useSerialize = true; //redis使用的是字符串结构存储，需要序列化
        } else {
            $this->cacheType = Cache::CACHE_TYPE_MEMCACHE; //使用memcache缓存
            $this->cacheConfig = \Yii::$app->params['memcache.main']; //缓存服务器配置
        }
        $this->prefixKey = UserModuleKey::GROUP_INFO; //缓存前缀key
        $this->useLevelOneCache = true; //是否开启一级缓存，同一个php进程生命周期，如果多次获取同个缓存key数据时会直接从内存一级缓存获取，不会再落到缓存服务器
    }
}
