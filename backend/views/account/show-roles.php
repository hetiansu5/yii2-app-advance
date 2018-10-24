<div class="container">
    <form class="form-horizontal" id="assign-role-form" method="post" action="/account/assign-roles">
        <input type="hidden" name="id" value="<?= $id;?>">
        <div class="panel">
            <div class="panel-body">
                <a class="btn" href="<?= $backUri;?>"><i class="icon icon-chevron-left"></i> 返回</a>
                <span style="margin: 0 10px;"><label>Email:</label><?= !empty($acctInfo['email']) ? $acctInfo['email'] : '';?></span>
                <span style="margin-right: 10px;"><label>真实姓名:</label><?= !empty($acctInfo['real_name']) ? $acctInfo['real_name'] : '';?></span>
                <input type="submit" class="btn btn-primary" value="保存">
            </div>
        </div>
        <div>
            <?php if($allRoles):?>
            <table class="table table-bordered">
                <tr>
                    <th>角色ID</th>
                    <th>角色名称</th>
                    <th>拥有权限</th>
                </tr>
                <?php foreach($allRoles as $info):?>
                    <tr>
                        <td>
                            <input type="checkbox" name="role_ids[]" value="<?= $info['id'];?>" <?= in_array($info['id'], $roleIds) ? 'checked' : '';?>>
                            <?= $info['id'];?>
                        </td>
                        <td><?= $info['name'];?></td>
                        <td><button type="button" class="btn" data-remote="/role/show-nodes?id=<?= $info['id'];?>" data-toggle="modal">查看角色权限</button></td>
                    </tr>
                <?php endforeach;?>
            </table>
            <?php endif;?>
        </div>
    </form>
</div>
<script>
    $(function () {
        $('#assign-role-form').myAjaxForm();
    });
</script>
