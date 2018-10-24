<?php

namespace backend\controllers;

use backend\components\BaseController;
use common\lib\RandString;
use common\lib\Validate;
use common\models\account\AccountModel;
use common\models\account\AccountRoleModel;
use common\models\account\RoleModel;

class AccountController extends BaseController
{

    protected $curModule = 'setting'; //必要。菜单有模块之分

    public function actionIndex()
    {
        $roleModel = RoleModel::instance();
        $accountModel = AccountModel::instance();
        $page = $this->getPage();
        $count = $this->getLimit();
        $roles = $roleModel->getList();
        $roleId = (int)$this->request->get('role_id');
        $email = (string)$this->request->get('email');
        $data = [];
        if ($roleId && !$email) {
            $accountRoleModel = AccountRoleModel::instance();
            $accountIds = $accountRoleModel->getAccountIdsByRoleId($roleId);
            if ($accountIds) {
                $data = $accountModel->getMulti($accountIds);
            }
        } elseif ($email) {
            $roleId = '';
            $result = $accountModel->getOneByEmail($email);
            if ($result) {
                $data[] = $result;
            }
        } else {
            $data = $accountModel->getList($page, $count);
        }
        $total = $accountModel->count();

        return $this->_assign('data', $data)
            ->_assign('total', $total)
            ->_assign('roles', $roles)
            ->_assign('roleId', $roleId)
            ->_assign('email', $email)
            ->_assign('statusNames', $accountModel->getStatusNames())
            ->_render();
    }

    public function actionShow()
    {
        $accountModel = AccountModel::instance();
        $id = (int)$this->getInput('id');
        if ($id) {
            $accountInfo = $accountModel->getOne($id);
            if (!$accountInfo) {
                $this->ajaxError('获取用户信息错误');
            }
        } else {
            $accountInfo = [];
        }

        return $this->_assign('accountInfo', $accountInfo)
            ->_renderPartial();
    }

    public function actionEdit()
    {
        $accountModel = AccountModel::instance();
        $id = (int)$this->getInput('id');
        if ($id) {
            $accountInfo = $accountModel->getOne($id);
            if (!$accountInfo) {
                $this->ajaxError('获取用户信息错误');
            }
        }

        $data = [
            'real_name' => $this->request->post('real_name'),
            'mobile' => (string)$this->request->post('mobile'),
            'is_readonly' => $this->request->post('readonly') ? 1 : 0,
            'is_manager' => $this->_getIsManager(),
        ];

        $password = trim($this->request->post('pwd'));

        if ($id) {
            if ($password) {
                if (strlen($password) < 6) {
                    $this->ajaxError('密码至少6位');
                }
                $data['password'] = $password;
            }
            $res = $accountModel->mUpdate($id, $data);
            $msg = "更新后台账号ID" . $id;
        } else {
            if (!$password || strlen($password) < 6) {
                $this->ajaxError('密码至少6位');
            }
            $data['password'] = $password;
            $data['email'] = $this->_getEmail();
            $data['create_act_id'] = $this->adminId;
            $res = $id = $accountModel->mInsert($data);
            $msg = "创建后台账号ID" . $id;
        }

        if (!$res) {
            $this->ajaxError('操作失败');
        }
        $this->addAdminLog($msg);
        $this->ajaxSuccess();
    }

    public function actionDelete()
    {
        $id = (int)$this->request->post('id');
        if (!$id) {
            $this->ajaxError('id无效');
        }
        if ($id == AccountModel::ROOT_ACCOUNT_ID) {
            $this->ajaxError('root账号不允许删除');
        }
        $accountModel = AccountModel::instance();
        if (!$accountModel->mDelete($id)) {
            $this->ajaxError();
        }
        $this->ajaxSuccess();
    }

    public function actionShowRoles()
    {
        $id = (int)$this->request->get('id');
        $backUri = (string)$this->request->get('back_uri');
        if (!$backUri) {
            $backUri = '/account/index';
        }
        $roleIds = [];
        $allRoles = [];
        $acctInfo = [];
        if ($id > 0) {
            $accountModel = AccountModel::instance();
            $acctInfo = $accountModel->getOne($id);
            $roleModel = RoleModel::instance();
            $allRoles = $roleModel->getAll();
            if ($allRoles) {
                $accountRoleModel = AccountRoleModel::instance();
                $roleIds = $accountRoleModel->getRoleIdsByAccountId($id);
            }
        }

        return $this->_assign('id', $id)
            ->_assign('allRoles', $allRoles)
            ->_assign('roleIds', $roleIds)
            ->_assign('backUri', $backUri)
            ->_assign('acctInfo', $acctInfo)
            ->_render();
    }

    public function actionAssignRoles()
    {
        $id = (int)$this->request->post('id');
        $accountInfo = AccountModel::instance()->getOne($id);
        if (!$accountInfo) {
            $this->ajaxError('账号不存在');
        }

        $roleIds = $this->request->post('role_ids');
        if (!$roleIds || !is_array($roleIds)) {
            $roleIds = [];
        }
        $accountRoleModel = AccountRoleModel::instance();
        if (!$accountRoleModel->assignRoles($id, $roleIds)) {
            $this->ajaxError();
        }
        $this->ajaxSuccess();
    }

    public function actionResetPwd()
    {
        $id = (int)$this->request->post('id');
        $accountModel = AccountModel::instance();
        $accountInfo = $accountModel->getOne($id);
        if (!$accountInfo) {
            $this->ajaxError('账号不存在');
        }

        $accountInfo = $accountModel->getOne($id);
        if (empty($accountInfo['email'])) {
            $this->ajaxError('无效id');
        }
        $newPwd = RandString::string(10);
        if (!$accountModel->resetPwd($id, $newPwd)) {
            $this->ajaxError();
        }
        $response = [
            'email' => $accountInfo['email'],
            'pwd' => $newPwd
        ];
        $this->ajaxSuccess($response);
    }

    private function _getEmail()
    {
        $result = $this->request->post('email');
        if (!$result || !Validate::isEmail($result)) {
            $this->ajaxError('邮箱未填写或格式错误');
        }
        //检测邮箱是否已存在
        if (AccountModel::instance()->getOneByEmail($result)) {
            $this->ajaxError('邮箱已存在');
        }
        return $result;
    }

    private function _getIsManager()
    {
        $result = $this->request->post('is_manager');
        if ($result && !AccountModel::isManager($this->adminInfo)) {
            $this->ajaxError('只有管理员有添加管理员权限');
        }
        return $result ? 1 : 0;
    }

}
