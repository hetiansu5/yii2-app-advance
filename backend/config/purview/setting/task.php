<?php
//控制器配置
return [
    'id' => 'task',
    'name' => '后台异步任务',
    'actions' => [
        'index' => [
            'name' => '任务处理列表',
            'is_menu' => 1
        ],
        'restart-daemon' => [
            'name' => '重启守护进程',
            'writable' => 1
        ],
        'restart-task' => [
            'name' => '重启指定任务处理进程',
            'writable' => 1
        ],
        'restart-all-tasks' => [
            'name' => '重启所有任务处理进程',
            'writable' => 1
        ],
        'set-task-running-switch' => [
            'name' => '设置指定任务是否启动',
            'writable' => 1
        ],
        'set-all-tasks-running-switch' => [
            'name' => '设置所有任务是否启动',
            'writable' => 1
        ],
        'set-worker-num' => [
            'name' => '设置指定任务处理进程数',
            'writable' => 1
        ],
    ]
];