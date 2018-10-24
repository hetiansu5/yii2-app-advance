<?php

namespace backend\controllers;

use backend\components\BaseController;
use common\lib\fw\Purview;
use common\models\account\RoleModel;

class RoleController extends BaseController
{

    protected $curModule = 'setting'; //必要。菜单有模块之分

    public function actionIndex()
    {
        $roleModel = RoleModel::instance();
        $page = $this->getPage();
        $limit = $this->getLimit();
        $extra = [
            'page' => $page,
            'limit' => $limit
        ];
        $condition = [];
        $data = $roleModel->getList($condition, [], [], $extra);
        $total = $roleModel->count($condition);
        return $this->_assign('data', $data)
            ->_assign('total', $total)
            ->_render();
    }

    public function actionShow()
    {
        $id = (int)$this->getInput('id');
        $roleModel = RoleModel::instance();
        $purview = Purview::getInstance();

        if ($id) {
            $info = $roleModel->getOne($id);
            if (!$info) {
                $this->ajaxError('数据不存在');
            }
            $checked = $purview->mergePrivileges([$info['nodes']]);
        } else {
            $info = [];
            $checked = [];
        }

        $nodes = $purview->getTreeNodes($purview->getAllNodes(), $checked);

        return $this->_assign('info', $info)
            ->_assign('nodes', $nodes)
            ->_renderPartial();
    }

    public function actionEdit()
    {
        $id = (int)$this->getInput('id');
        $roleModel = RoleModel::instance();

        if ($id) {
            $info = $roleModel->getOne($id);
            if (!$info) {
                $this->ajaxError('数据不存在');
            }
        }

        $data = [
            'name' => (string)$this->getInput('name'),
            'status' => $this->getInput('status') ? RoleModel::STATUS_ENABLE : RoleModel::STATUS_DISABLE,
            'nodes' => implode(',', $this->getInput('nodes'))
        ];

        if ($id) {
            $res = $roleModel->mUpdate($id, $data);
            $msg = "更新角色ID" . $id;
        } else {
            $res = $id = $roleModel->mInsert($data);
            $msg = "创建角色ID" . $id;
        }

        if ($res === false) {
            $this->ajaxError('操作失败');
        }

        $this->addAdminLog($msg);
        $this->ajaxSuccess();
    }

    public function actionDelete()
    {
        $id = (int)$this->request->post('id');
        $model = RoleModel::instance();
        $info = $model->getOne($id);
        if (!$info) {
            $this->ajaxError('获取信息失败!');
        }

        if (!$model->mDelete($id)) {
            $this->ajaxError('操作失败');
        }
        $this->ajaxSuccess();
    }


    public function actionSetStatus()
    {
        $id = (int)$this->request->post('id');
        $model = RoleModel::instance();
        $info = $model->getOne($id);
        if (!$info) {
            $this->ajaxError('获取信息失败!');
        }

        $update = [
            'status' => $info['status'] == RoleModel::STATUS_DISABLE
                ? RoleModel::STATUS_ENABLE
                : RoleModel::STATUS_DISABLE
        ];
        if (!$model->mUpdate($id, $update)) {
            $this->ajaxError('操作失败');
        }
        $this->ajaxSuccess();
    }

    public function actionShowNodes()
    {
        $roleId = (int)$this->request->get('id');
        $checkedPrivileges = [];
        $purview = Purview::getInstance();
        if ($roleId > 0) {
            $roleModel = RoleModel::instance();
            $roleInfo = $roleModel->getOne($roleId);
            if (isset($roleInfo['nodes'])) {
                $checkedPrivileges = $purview->mergePrivileges([$roleInfo['nodes']]);
            }
        }

        $nodes = $purview->getTreeNodes($purview->getAllNodes(), $checkedPrivileges);
        $this->_assign('nodes', $nodes)
            ->_render();
    }

}
