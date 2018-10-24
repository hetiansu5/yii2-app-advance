<?php
use common\models\account\RoleModel;
?>
<link rel="stylesheet" href="/ztree/zTreeStyle.css">
<script src="/ztree/js/jquery.ztree.all-3.5.min.js"></script>
<script>
    var zTreeObj;
    // zTree 的参数配置，深入使用请参考 API 文档（setting 配置详解）
    var setting = {
        check : { enable: true, autoCheckTrigger: true }
    };
    // zTree 的数据属性，深入使用请参考 API 文档（zTreeNode 节点数据详解）
    var zNodes = <?php echo json_encode($nodes); ?>;

    $(function(){
        $.fn.zTree.init($("#treePrivilege"), setting, zNodes);
        $("#group-save").click(function(){
            var zTreeObj = $.fn.zTree.getZTreeObj('treePrivilege');
            var nodes = zTreeObj.getNodes();
            function getIdArr(nodes, idArr) {
                for (var key in nodes) {
                    var node = nodes[key];
                    //node.check_Child_State: -1无子级;0子级无选中;1子级选中部分;2子级全部选中;
                    if (node.checked) {
                        if (node.check_Child_State == 2 || node.check_Child_State == -1) {
                            idArr.push(node.id);
                            continue;
                        } else if (node.isParent) {
                            getIdArr(node.children, idArr);
                        }
                    }
                }
            }
            var nodeIds = [];
            getIdArr(nodes, nodeIds);
            $.post(
                '/role/edit',
                {
                    id      : '<?php echo $info['id']; ?>',
                    name    : $("input[name='name']").val(),
                    status  : $("input[name='status']:checked").val(),
                    nodes   : nodeIds,
                    submit  : 1
                },
                function (resp) {
                    if (resp.code != undefined) {
                        if (resp.code == 0) {
                            alert('操作成功');
                            location.reload();
                        } else if (resp.msg != undefined && resp.msg) {
                            alert(resp.msg);
                        } else {
                            alert('操作失败');
                        }
                    } else {
                        alert('返回结果异常');
                    }
                },
                'json'
            );

        })
    });
</script>
<div class="row">
    <form class="form-horizontal">
        <div class="form-group">
            <label class="col-sm-2 required">组名</label>
            <div class="col-md-6 col-sm-10">
                <input type="text" class="form-control" name="name" value="<?php echo $info['name'];?>" />
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-2">状态</label>
            <div class="col-sm-10">
                <div class="radio">
                    <label>
                        <input type="radio" name="status" value="<?=RoleModel::STATUS_ENABLE?>" <?php echo $info['status'] == RoleModel::STATUS_ENABLE ? 'checked' : '';?>/> 启用
                    </label>
                    <label>
                        <input type="radio" name="status" value="<?=RoleModel::STATUS_DISABLE?>" <?php echo $info['status'] != RoleModel::STATUS_ENABLE ? 'checked' : '';?>/> 禁用
                    </label>
                </div>
            </div>
        </div>
        <div class="form-group">
            <label for="exampleInputAccount4" class="col-sm-2">权限</label>
            <div class="col-sm-10 ztree" id="treePrivilege"></div>
        </div>

        <div class="form-group">
            <div class="col-sm-offset-2 col-sm-10">
                <input type="hidden" name="id" value="<?=$info['id']?>">
                <button type="button" name="submit" id="group-save" value="1" class="btn btn-default">保存</button>
            </div>
        </div>
    </form>
</div>