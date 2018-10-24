<?php
//控制器配置
return [
    'id' => 'account',
    'name' => '后台账号管理',
    'actions' => [
        'index' => [
            'name' => '列表',
            'is_menu' => 1
        ],
        'show' => [
            'name' => '查看',
        ],
        'add' => [
            'name' => '添加',
            'writable' => 1
        ],
        'edit' => [
            'name' => '编辑',
            'writable' => 1
        ],
        'delete' => [
            'name' => '删除',
            'writable' => 1
        ],
        'show-roles' => [
            'name' => '查看分配的角色'
        ],
        'assign-roles' => [
            'name' => '分配角色',
            'writable' => 1
        ],
        'reset-pwd' => [
            'name' => '重置密码',
            'writable' => 1
        ],
    ]
];