<?php
use common\models\account\AccountModel;

?>
<div class="row">
    <form>
        <div class="col-md-2">
            <select name="role_id" class="form-control">
                <option value="">所属角色</option>
                <?php foreach ($roles as $role): ?>
                    <option value="<?php echo $role['id']; ?>" <?php echo $roleId == $role['id'] ? 'selected' : ''; ?>><?php echo $role['name']; ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-2">
            <input type="text" name="email" class="form-control" value="<?php echo !empty($email) ? $email : ''; ?>"
                   placeholder="Email">
        </div>
        <div class="col-md-2">
            <input type="submit" class="btn btn-primary" value="确定">
        </div>
        <div class="col-xs-1 pull-right">
            <button type="button" class="btn btn-primary" data-remote="/account/show?is_modal=1" data-toggle="modal">
                新建账号
            </button>
        </div>
    </form>
</div>
<p></p>
<table class="table table-bordered">
    <tr>
        <th>账号ID</th>
        <th>Email</th>
        <th>真实姓名</th>
        <th>用户状态</th>
        <th>是否只读</th>
        <th>创建时间</th>
        <th>操作</th>
    </tr>
    <?php if ($data): ?>
        <?php foreach ($data as $acct): ?>
            <tr data-id="<?= $acct['id']; ?>">
                <td><?= $acct['id']; ?></td>
                <td><?= $acct['email']; ?></td>
                <td><?= $acct['real_name']; ?></td>
                <td><?= !empty($statusNames[$acct['status']]) ? $statusNames[$acct['status']] : $acct['status']; ?></td>
                <td><?= !empty($acct['is_readonly']) ? '是' : '否'; ?></td>
                <td>
                    <?php $datetime = !empty($acct['created_at']) ? date('Y-m-d H:i:s', $acct['created_at']) : ''; ?>
                    <?php if ($datetime): ?>
                        <span data-toggle="tooltip"
                              title="<?= $datetime; ?>"><?= date('Y-m-d', $acct['created_at']); ?></span>
                    <?php endif; ?>
                </td>
                <td>
                    <div>
                        <?php if (!AccountModel::isManager($acct)): ?>
                            <button type="button" class="btn btn-sm btn-primary"
                                    data-remote="/account/show?id=<?= $acct['id']; ?>" data-toggle="modal">编辑
                            </button>
                            <a class="btn btn-sm"
                               href="/account/show-roles?id=<?= $acct['id']; ?>&back_uri=<?= urlencode($_SERVER['REQUEST_URI']); ?>">分配角色</a>
                        <?php endif; ?>

                        <?php if (AccountModel::isManager($adminInfo)): ?>
                            <button type="button" class="btn btn-sm btn-danger js-delete-acct">删除</button>
                        <?php endif; ?>
                    </div>
                </td>
            </tr>
        <?php endforeach; ?>
    <?php endif; ?>
</table>
<div id="pager" data-total="<?= $total; ?>" data-item-count="<?= count($data); ?>"></div>
<script>
    $(function () {
        $('#pager').pager();
        $('[data-toggle="tooltip"]').tooltip();

        $('.js-reset-pwd').click(function () {
            if (!confirm('确定重置密码?')) {
                return false;
            }
            var id = $(this).parents('tr').data('id');
            $.myAjaxPost(
                '/account/reset_pwd',
                {id: id},
                {
                    success: function (resp) {
                        if (resp.code != undefined) {
                            if (resp.code == 0) {
                                if (resp.pwd !== undefined && resp.email != undefined) {
                                    alert('操作成功,账号:' + resp.email + ',新密码:' + resp.pwd);
                                } else {
                                    alert('操作成功');
                                }
                            } else if (resp.msg != undefined && resp.msg) {
                                alert(resp.msg);
                            } else {
                                alert('操作失败');
                            }
                        } else {
                            alert('返回结果异常');
                        }
                    }
                }
            );
        });

        $('.js-delete-acct').click(function () {
            if (!confirm('确定删除?')) {
                return false;
            }
            var id = $(this).parents('tr').data('id');
            $.myAjaxPost('/account/delete', {id: id});
        })
    });
</script>