<?php
namespace common\models\account;

use common\models\BaseModel;

class RoleModel extends BaseModel
{

    const STATUS_ENABLE = 1; //启用
    const STATUS_DISABLE = 0; //禁用

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%role}}';
    }

    /**
     * 删除角色表中记录的同时，还要删掉账号角色关联表中的对应数据
     * @param int $id
     * @return bool
     */
    public function mDelete($id)
    {
        $transaction = self::getDb()->beginTransaction();
        $res = parent::mDelete($id);
        if (!$res) {
            $transaction->rollBack();
            return false;
        }
        $accountRoleModel = AccountRoleModel::instance();
        $res = $accountRoleModel->deleteByRoleId($id);
        if ($res === false) {
            $transaction->rollBack();
            return false;
        }
        $transaction->commit();
        return true;
    }

    public function getAll()
    {
        $condition = [];
        $extra = [
            'limit' => 1000
        ];
        return $this->getList($condition, [], null, $extra);
    }

}