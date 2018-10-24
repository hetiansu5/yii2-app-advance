<?php
namespace common\cache;

use common\lib\fw\CacheTrait as FwCacheTrait;

/**
 * 支持分页数据缓存
 * @author hts
 */
trait CacheTrait
{
    use FwCacheTrait {
        getMulti as protected _getMulti;
        get as protected __get;
        set as protected __set;
        delete as protected __delete;
    }

    public function getMulti(array $idArr, array &$idsByMiss = [])
    {
        //function key($id) 可能被重写
        $keyMaps = [];
        foreach ($idArr as $id) {
            $key = $this->key($id);
            $keyMaps[$key] = $id;
        }
        $result = $this->_getMulti($idArr, $idsByMiss);
        $formatResult = [];
        if (!empty($result)) {
            foreach ($result as $key => $value) {
                $formatResult[$keyMaps[$key]] = $value;
            }
        }
        return $formatResult;
    }

    public function depKey($depId)
    {
        return $this->prefixKey . 'd:' . $depId;
    }

    public function getDep($depId)
    {
        return $this->__get($this->depKey($depId));
    }

    public function setDep($depId)
    {
        $depValue = time();
        $result = $this->__set($this->depKey($depId), $depValue);

        if (!$result) {
            return false;
        }

        return $depValue;
    }

    public function deleteDep($depId)
    {
        return $this->__delete($this->depKey($depId));
    }

    public function withDepKey($id, $depValue)
    {
        return $id . ':' . $depValue;
    }

    public function getWithDep($id, $depId)
    {
        $depValue = $this->getDep($depId);
        if (!$depValue) {
            return false;
        }
        return $this->__get($this->withDepKey($id, $depValue));
    }

    public function setWithDep($id, $depId, $value, $ttl = 0)
    {
        $depValue = $this->getDep($depId);
        if (!$depValue) {
            $depValue = $this->setDep($depId);
            if (!$depValue) {
                return false;
            }
        }
        $key = $this->withDepKey($id, $depValue);
        $result = $this->__set($key, $value, $ttl);
        if (!$result) {
            return false;
        }
        return true;
    }


}