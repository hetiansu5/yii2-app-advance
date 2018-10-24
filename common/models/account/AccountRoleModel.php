<?php
namespace common\models\account;

use common\models\BaseModel;

/**
 * 账号权限
 * @author hts
 */
class AccountRoleModel extends BaseModel
{

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%account_role}}';
    }

    /**
     * @param $accountId
     * @param array $roleIdArr
     * @return bool
     */
    public function insertMulti($accountId, array $roleIdArr)
    {
        if (!$roleIdArr) {
            return false;
        }
        $data = [];
        foreach ($roleIdArr as $roleId) {
            $data[] = [
                'account_id' => $accountId,
                'role_id' => $roleId
            ];
        }
        return self::getDb()->createCommand()->batchInsert(static::tableName(), ['account_id', 'role_id'], $data)->execute();
    }

    /**
     * @param $roleId
     * @return int
     */
    public function deleteByRoleId($roleId)
    {
        $condition = [
            'role_id' => $roleId
        ];
        return self::deleteAll($condition);
    }

    /**
     * @param $accountId
     * @return array
     */
    public function getRoleIdsByAccountId($accountId)
    {
        $condition = [
            'account_id' => $accountId
        ];
        $list = $this->getList($condition);
        return $list ? array_column($list, 'role_id') : [];
    }

    /**
     * @param $roleId
     * @return array
     */
    public function getAccountIdsByRoleId($roleId)
    {
        $condition = [
            'role_id' => $roleId
        ];
        $list = $this->getList($condition);
        return $list ? array_column($list, 'account_id') : [];
    }

    public function getRoleListByAccountId($accountId)
    {
        $condition = [
            'account_id' => $accountId
        ];
        $list = $this->getList($condition);

        if (!$list) {
            return $list;
        }

        $roleIdArr = array_column($list, 'role_id');

        $condition = [
            'and',
            ['in', 'id', $roleIdArr],
            'status=1'
        ];
        $roleModel = RoleModel::instance();
        return $roleModel->getList($condition);
    }

    /**
     * @param $accountId
     * @param array $roleIdArr
     * @return bool
     */
    public function assignRoles($accountId, array $roleIdArr)
    {
        $condition = [
            'account_id' => $accountId
        ];
        $list = $this->getList($condition);

        $roleMap = array_flip($roleIdArr);

        if ($list) {
            foreach ($list as $key => $val) {
                if (isset($roleMap[$val['role_id']])) {
                    unset($roleMap[$val['role_id']]);
                    unset($list[$key]);
                }
            }

            if ($list) {
                $idArr = array_column($list, 'id');
                $condition = ['in', $this->primaryKey, $idArr];
                self::deleteAll($condition);
            }
        }

        if ($roleMap) {
            $roleIdArr = array_keys($roleMap);
            $this->insertMulti($accountId, $roleIdArr);
        }
        return true;
    }
}