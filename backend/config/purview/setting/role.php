<?php
//控制器配置
return [
    'id' => 'role',
    'name' => '角色管理',
    'actions' => [
        'index' => [
            'name' => '列表',
            'is_menu' => 1
        ],
        'show' => [
            'name' => '查看', //是否显示到左侧菜单中
        ],
        'edit' => [
            'name' => '编辑',
            'writable' => 1 //写操作，若账号为只读则无权限
        ],
        'delete' => [
            'name' => '删除',
            'writable' => 1
        ],
        'show-nodes' => [
            'name' => '查看某角色的权限',
        ],
    ]
];