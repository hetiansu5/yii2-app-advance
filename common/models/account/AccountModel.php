<?php
namespace common\models\account;

use common\lib\Functions;
use common\models\BaseModel;

class AccountModel extends BaseModel
{

    const ROOT_ACCOUNT_ID = 1;

    //用户类型 目前未使用
    const TYPE_NORMAL = 0; //需要通过email,password进行登录的账号
    const TYPE_OA = 1; //需要通过OA系统授权登录的账号

    //用户状态
    const STATUS_NORMAL = 0; //正常
    const STATUS_DISABLED = 1; //禁用

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%account}}';
    }

    public function getStatusNames()
    {
        return [
            self::STATUS_NORMAL => '正常',
            self::STATUS_DISABLED => '禁用',
        ];
    }

    /**
     * @param array $info
     * @return false|int
     */
    public function mInsert($info)
    {
        if (!isset($info['created_at'])) {
            $info['created_at'] = time();
        }
        if (!isset($info['updated_at'])) {
            $info['updated_at'] = time();
        }
        $info['password'] = $this->_encodePassword($info['password']);
        return parent::mInsert($info);
    }

    /**
     * @param int $id
     * @param array $info
     * @return int
     */
    public function mUpdate($id, $info)
    {
        if (!isset($info['updated_at'])) {
            $info['updated_at'] = time();
        }
        if (isset($info['password'])) {
            $info['password'] = $this->_encodePassword($info['password']);
        }
        return parent::mUpdate($id, $info);
    }

    public function checkLogin($email, $password)
    {
        $result = $this->getOneByEmail($email);
        if (isset($result['id'], $result['password']) && $this->checkPassword($password, $result['password'])) {
            return $result;
        }
        return null;
    }

    public function insertRootAccount($email, $password)
    {
        $info = [
            'id' => self::ROOT_ACCOUNT_ID,
            'email' => $email,
            'password' => $this->_encodePassword($password),
            'real_name' => 'root'
        ];
        return $this->insert($info) > 0;
    }

    public function getOneByEmail($email)
    {
        $condition = [
            'email' => $email
        ];
        return $this->getOneByCondition($condition);
    }

    public function resetPwd($id, $newPwd)
    {
        $updateInfo = [
            'password' => $this->_encodePassword($newPwd)
        ];
        return $this->update($id, $updateInfo);
    }

    public static function isManager($accountInfo)
    {
        return $accountInfo['is_manager'] ? true : false;
    }

    /**
     * 批量获取后台管理员用户名
     * @param array $idArr
     * @return array
     */
    public function getAdminList($idArr)
    {
        $list = $this->getMulti($idArr);
        return Functions::listToMap($list, 'id');
    }

    /**
     * @param $password
     * @param $hash
     * @return bool
     */
    public function checkPassword($password, $hash)
    {
        return password_verify($password, $hash);
    }

    /**
     * @param $password
     * @return bool|string
     */
    private function _encodePassword($password)
    {
        return password_hash($password, PASSWORD_BCRYPT);
    }

}