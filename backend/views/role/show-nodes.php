<link rel="stylesheet" href="/ztree/zTreeStyle.css">
<script src="/ztree/js/jquery.ztree.all-3.5.min.js"></script>
<script>
    var setting = {
        check: { enable: true },
        callback: {
            beforeCheck: function(treeId, treeNode){
                return false;
            }
        }
    };
    var zNodes = <?= json_encode($nodes);?>;
    $(function(){
        $.fn.zTree.init($("#treePrivilege"), setting, zNodes);
    });
</script>
<div>
    <ul id="treePrivilege" class="ztree"></ul>
</div>