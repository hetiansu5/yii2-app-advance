<?php
use common\models\account\RoleModel;

?>
<div>
    <div>
        <button type="button" class="btn btn-primary" data-remote="/role/show" data-toggle="modal">添加角色</button>
    </div>
    <p></p>
    <?php if ($data): ?>
        <table class="table table-bordered">
            <thead>
            <tr>
                <th>角色ID</th>
                <th>角色名称</th>
                <th>状态</th>
                <th>操作</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($data as $value): ?>
                <tr data-id="<?php echo $value['id']; ?>">
                    <td><?php echo $value['id']; ?></td>
                    <td><?php echo $value['name']; ?></td>
                    <td>
                        <?php if ($value['status']): ?>
                            <span class="label label-success">启用</span>
                        <?php else: ?>
                            <span class="label label-danger">禁用</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <button type="button" class="btn btn-sm btn-primary"
                                data-remote="/role/show?id=<?php echo $value['id']; ?>" data-toggle="modal">编辑
                        </button>
                        <?php if ($value['status'] == RoleModel::STATUS_DISABLE): ?>
                            <button type="button" class="btn btn-sm btn-success btn-set-group">启用</button>
                        <?php else: ?>
                            <button type="button" class="btn btn-sm btn-danger btn-set-group">禁用</button>
                        <?php endif; ?>
                        <button type="button" class="btn btn-sm btn-danger btn-del-group">删除</button>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        <div id="pager" data-total="<?= $total; ?>" data-item-count="<?= count($data); ?>"></div>
    <?php endif; ?>
</div>
<script>
    $(function () {
        $('#pager').pager();

        $(".btn-set-group,.btn-del-group").click(function () {
            var id = $(this).parents("tr").data("id");
            var url = $(this).hasClass('btn-set-group') ? '/role/set-status' : '/role/delete';
            if (confirm('确认修改?')) {
                $.post(
                    url, {id: id},
                    function (res) {
                        alert(res.msg);
                        if (res.code == '0') {
                            location.reload();
                        }
                    }, 'json'
                )
            }
        });
    });
</script>