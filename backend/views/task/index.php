<div class="mat-page-title">
    <h1>后台异步任务列表</h1>
</div>
<div class="panel mat-table-view">
    <div class="panel-body">
        <div class="mat-toolbar">
            <button type="button" class="btn btn-primary" id="btn-run-all-tasks">全部启动</button>
            <button type="button" class="btn btn-primary" id="btn-stop-all-tasks">全部暂停</button>
            <button type="button" class="btn btn-primary" id="btn-restart-all-tasks">全部重启</button>
            <button type="button" class="btn btn-primary" id="btn-restart-daemon">重启守护进程</button>
        </div>
        <div class="table-responsive">
            <?php if ($data): ?>
                <style>
                    input.form-control.worker-num {
                        width: 80px;
                    }
                </style>
                <table class="table table-bordered">
                    <thead>
                    <tr>
                        <th>任务名称</th>
                        <th>队列类型</th>
                        <th>是否启动</th>
                        <th>上次重启时间</th>
                        <th>worker数</th>
                        <th>积压数</th>
                        <th>操作</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($data as $item): ?>
                        <tr data-job-name="<?= $item['job_name']; ?>">
                            <td><?= $item['job_name']; ?></td>
                            <td><?= $item['queue_type']; ?></td>
                            <td><?= !empty($item['is_run']) ? '是' : '否'; ?></td>
                            <td><?= !empty($item['restart_time']) ? date('Y-m-d H:i:s', $item['restart_time']) : ''; ?></td>
                            <td>
                                <input type="text" value="<?= !empty($item['worker_num']) ? $item['worker_num'] : 0; ?>"
                                       class="worker-num js-worker-num form-control inline">
                                <button type="button" class="btn js-set-worker-num">确定</button>
                            </td>
                            <td><?= !empty($item['backlog']) ? $item['backlog'] : 0; ?></td>
                            <td>
                                <div>
                                    <button type="button" class="btn js-run-task">启动</button>
                                    <button type="button" class="btn js-stop-task">暂停</button>
                                    <button type="button" class="btn js-restart-task">重启</button>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>
</div>
<script>
    $(function () {
        $('#btn-run-all-tasks').click(function () {
            $.myAjaxPost(
                '/task/set-all-tasks-running-switch',
                {is_run: 1}
            );
        });

        $('#btn-stop-all-tasks').click(function () {
            $.myAjaxPost(
                '/task/set-all-tasks-running-switch',
                {is_run: 0}
            );
        });

        $('#btn-restart-all-tasks').click(function () {
            $.myAjaxPost(
                '/task/restart-all-tasks'
            );
        });

        $('#btn-restart-daemon').click(function () {
            $.myAjaxPost(
                '/task/restart-daemon'
            );
        });

        $('.js-run-task').click(function () {
            var jobName = $(this).parents('tr').data('job-name');
            $.myAjaxPost(
                '/task/set-task-running-switch',
                {job_name: jobName, is_run: 1}
            );
        });

        $('.js-stop-task').click(function () {
            var jobName = $(this).parents('tr').data('job-name');
            $.myAjaxPost(
                '/task/set-task-running-switch',
                {job_name: jobName, is_run: 0}
            );
        });

        $('.js-restart-task').click(function () {
            var jobName = $(this).parents('tr').data('job-name');
            $.myAjaxPost(
                '/task/restart-task',
                {job_name: jobName}
            );
        });

        $('.js-set-worker-num').click(function () {
            var $parent = $(this).parents('tr');
            var jobName = $parent.data('job-name');
            var workerNum = $parent.find('.js-worker-num').val();
            $.myAjaxPost(
                '/task/set-worker-num',
                {job_name: jobName, worker_num: workerNum}
            );
        });
    });
</script>