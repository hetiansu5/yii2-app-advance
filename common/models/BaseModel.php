<?php
namespace common\models;

use common\cache\CacheTrait;
use common\traits\ModelTrait;
use yii\db\ActiveRecord;

/**
 * 基础Model，封装了一些基础的调用方法
 * @author hts
 */
abstract class BaseModel extends ActiveRecord
{

    use ModelTrait;

    protected $primaryKey = 'id';

    /**
     * @var CacheTrait
     */
    protected $cache;

    /**
     * 插入
     * @param array $info
     * @return int|bool|null  false表示出错了，int为主键ID
     */
    public function mInsert($info)
    {
        try {
            $primaryKeys = static::getDb()->schema->insert(static::tableName(), $info);
        } catch (\Exception $e) {
            $this->handleException($e);
            return false;
        }

        return isset($primaryKeys[$this->primaryKey]) ? $primaryKeys[$this->primaryKey] : null;
    }

    /**
     * 更新
     * @param int $id
     * @param array $info
     * @return int|bool  false表示出错了，int为受影响的记录数
     */
    public function mUpdate($id, $info)
    {
        $condition = [
            $this->primaryKey => $id
        ];
        //返回受影响的记录数
        try {
            $res = self::updateAll($info, $condition);
        } catch (\Exception $e) {
            $this->handleException($e);
            return false;
        }
        return $res;
    }

    /**
     * 删除
     * @param int $id
     * @return int|bool  false表示出错了，int为受影响的记录数
     */
    public function mDelete($id)
    {
        $condition = [
            $this->primaryKey => $id
        ];
        //返回受影响的记录数
        try {
            $res = self::deleteAll($condition);
        } catch (\Exception $e) {
            $this->handleException($e);
            return false;
        }
        return $res;
    }

    /**
     * 批量插入
     * @param $rows
     * @return int
     */
    public function insertBatch($rows)
    {
        $column = array_keys(reset($rows));
        try {
            $res = self::getDb()->createCommand()->batchInsert(static::tableName(), $column, $rows)->execute();
        } catch (\Exception $e) {
            $this->handleException($e);
            return false;
        }
        return $res;
    }

    /**
     * 批量删除
     * @param $idArr
     * @return int
     */
    public function deleteMulti($idArr)
    {
        $condition = ['in', $this->primaryKey, $idArr];
        //返回受影响的记录数
        try {
            $res = self::deleteAll($condition);
        } catch (\Exception $e) {
            $this->handleException($e);
            return false;
        }
        return $res;
    }

    /**
     * 批量更新
     * @param $idArr
     * @param $data
     * @return bool|int
     */
    public function updateMulti($idArr, $data)
    {
        $condition = ['in', $this->primaryKey, $idArr];
        try {
            $res = self::updateAll($data, $condition);
        } catch (\Exception $e) {
            $this->handleException($e);
            return false;
        }
        return $res;

    }

    /**
     * @param $condition
     * @param array $params
     * @return array|null|bool false表示出错了，null表示无记录，array表示有记录
     */
    public function getOneByCondition($condition, $params = [])
    {
        try {
            $info = self::find()->where($condition, $params)->asArray()->one(); //返回数组形式
        } catch (\Exception $e) {
            $this->handleException($e);
            return false;
        }
        return $info;
    }

    /**
     * getOne可能会被重写
     * @param $id
     * @return array|null
     */
    protected function _getOne($id)
    {
        $condition = [
            $this->primaryKey => $id
        ];
        return $this->getOneByCondition($condition);
    }

    /**
     * 获取单个ID的数据
     * @param int $id
     * @return mixed
     */
    public function getOne($id)
    {
        return $this->_getOne($id);
    }

    /**
     * 批量获取
     * @param $filed
     * @param $idArr
     * @return array|bool|\yii\db\ActiveRecord[]
     */
    public function getMultiByField($filed, $idArr)
    {
        $condition = ['in', $filed, $idArr];
        try {
            $list = self::find()->where($condition)->asArray()->all(); //返回数组形式
        } catch (\Exception $e) {
            $this->handleException($e);
            return false;
        }
        return $list;
    }

    /**
     * getMulti可能会被重写
     * @param $idArr
     * @return array|null|false  false表示出错了，null表示无记录，array表示有记录
     */
    protected function _getMulti($idArr)
    {
        $condition = ['in', $this->primaryKey, $idArr];
        try {
            $list = self::find()->where($condition)->asArray()->all(); //返回数组形式
        } catch (\Exception $e) {
            $this->handleException($e);
            return false;
        }
        return $list;
    }

    /**
     * 批量获取多个ID的数据
     * @param array $idArr
     * @return mixed 返回索引数组
     */
    public function getMulti($idArr)
    {
        return $this->_getMulti($idArr);
    }

    /**
     * 先缓存获取数据，找不到再从db获取
     * @param $id
     * @param CacheTrait $cache
     * @return bool|mixed
     */
    protected function getOneByCache($id, CacheTrait $cache = null)
    {
        $cache = $cache ?: $this->cache;
        $info = $cache->get($id);

        if (!$info) {
            $info = $this->_getOne($id);
            if ($info) {
                $cache->set($id, $info);
            }
        }

        return $info;
    }

    /**
     * 先缓存获取数据，找不到再从db获取
     * @param $idArr
     * @param CacheTrait $cache
     * @return mixed 这里要注意一下，返回的不是索引数组，是关联数组，键为id值，可能存在为null值的key
     */
    protected function getMultiByCache($idArr, CacheTrait $cache = null)
    {
        $cache = $cache ?: $this->cache;
        $idsByMiss = [];
        $list = [];
        foreach ($idArr as $id) { //保持原来键值的顺序
            $list[$id] = null;
        }

        $cacheList = $cache->getMulti($idArr, $idsByMiss);
        foreach ($cacheList as $k => $v) {
            $list[$k] = $v;
        }

        if ($idsByMiss) {
            $missList = $this->_getMulti($idsByMiss);
            if ($missList) {
                $addList = [];
                foreach ($missList as $v) {
                    $addList[$v[$this->primaryKey]] = $v;
                    $list[$v[$this->primaryKey]] = $v;
                }
                $cache->setMulti($addList);
            }
        }
        return $list;
    }

    /**
     * 计数
     * @param mixed $condition
     * @param array $params
     * @return int|bool  false表示出错了，int为正常计数值
     */
    public function count($condition = [], $params = [])
    {
        $query = self::find();
        if ($condition) { //查询条件
            $query->where($condition, $params);
        }
        try {
            $count = $query->count();
        } catch (\Exception $e) {
            $this->handleException($e);
            return false;
        }
        return is_numeric($count) ? (int)$count : $count;
    }

    /**
     * 获取列表
     * @param mixed $condition 查询条件
     * @param array $params 参数绑定
     * @param array|null $fields 获取字段
     * @param array $extra ["page": 1, "limit": 1, "order_by" => ["create_time" => SORT_DES]] 分页|分页条目数|排序方式
     * @return array|null|false  false表示出错了，null表示无记录，array表示有记录
     */
    public function getList($condition = [], $params = [], $fields = [], $extra = [])
    {
        $query = self::find();
        if ($fields) { //获取字段
            $query->select($fields);
        }
        if ($condition) { //查询条件
            $query->where($condition, $params);
        }
        if ($extra['order_by']) { //排序方式
            $query->orderBy($extra['order_by']);
        }
        if ($extra['limit'] > 0) { //分页条目数
            $query->limit($extra['limit']);
            if ($extra['page'] >= 1) { //分页
                $query->offset(($extra['page'] - 1) * $extra['limit']);
            }
        }
        try {
            $list = $query->asArray()->all(); //返回数组形式
        } catch (\Exception $e) {
            $this->handleException($e);
            return false;
        }
        return $list;
    }

    /**
     * 将自定义的where条件转换为condition和params形式
     * @param array $where eg. ['email' => '1@163.com', 'status' => 1, 'name like' => '我的%', 'age >' => 2]
     * @return array
     */
    public function buildConditionParams($where)
    {
        $condition = [
            'and'
        ];
        $params = [];
        foreach ($where as $key => $val) {
            $keys = explode(' ', $key, 2);
            $keys = array_filter($keys);
            $operate = !empty($keys[1]) ? $keys[1] : '=';
            $hash = substr(md5($key), 0, 10);
            $condition[] = "{$keys[0]} {$operate} :{$hash}";
            $params[":{$hash}"] = $val;
        }
        return [$condition, $params];
    }

    /**
     * @param null $page
     * @param null $limit
     * @param $orderBy
     * @return array
     */
    public function buildExtra($page = null, $limit = null, $orderBy)
    {
        $extra = [];
        if (is_numeric($limit) && $limit >= 1) {
            $extra['limit'] = $limit;
        }
        if (is_numeric($page) && $page >= 1) {
            $extra['page'] = $page;
        }
        if (is_array($orderBy)) {
            $extra['order_by'] = $orderBy;
        }
        return $extra;
    }

    /**
     * 统计
     * @param $fields
     * @param mixed $condition
     * @param array $params
     * @return int|bool  false表示出错了，int为正常计数值
     */
    public function sum($fields, $condition = [], $params = [])
    {
        $query = self::find()->select([$fields=>"SUM($fields)"]);
        if ($condition) { //查询条件
            $query->where($condition, $params);
        }
        try {
            $sum = $query->asArray()->one();
        } catch (\Exception $e) {
            $this->handleException($e);
            return false;
        }
        return (int)$sum[$fields];
    }

}
