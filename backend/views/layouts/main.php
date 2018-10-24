<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=Edge,chrome=1">
    <meta name="renderer" content="webkit" />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <meta name="keywords" content="" />
    <meta name="description" content="" />
    <link rel="stylesheet" href="/common/zui/1.7.0/dist/css/zui.min.css">
    <link rel="stylesheet" href="/common/mat/mat.min.css">
    <link rel="stylesheet" href="/css/admin.css">
    <!--    <link rel="shortcut icon" href="" type="image/x-icon" />-->
    <title>管理后台</title>
    <script src="/common/mat/mat.min.js"></script>
    <script src="/js/jquery.form.js"></script>
    <script src="/js/admin.js"></script>
    <script>
        $(function() {
            // 初始化 MAT
            window.$MAT = MAT.init();

            // $MAT 实例对象有控制界面的一些方法
            // $.zui 为 ZUI 框架的 jQuery 扩展对象，包含相应的组件和操作方法
//            console.log($MAT, $.zui.Messager);
        });
    </script>
</head>
<body>
<!-- 整体布局 Start -->
<div id="mat-layout" class="min-width header-fixed footer-fixed side-fixed _side-fixed _side-narrow">

    <!-- 顶栏 Start -->
    <header id="mat-header">
        <a href="/" class="pull-left" id="mat-brand">
            <!--<img src="" class="brand-logo"/>-->
            <div class="brand-title">
                <div class="brand-text">管理后台</div>
            </div>
        </a>
        <nav id="mat-header-nav" data-dropdown="hover">
            <ul class="nav navbar-nav pull-right">
                <?php $adminInfo = $this->params['adminInfo']; ?>
                <?php if(!empty($adminInfo)):?>
                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                            <i class="icon icon-user"></i>
                            <?= !empty($adminInfo['real_name']) ? $adminInfo['real_name'] : (!empty($adminInfo['email']) ? $adminInfo['email'] : '');?>
                            <b class="caret"></b>
                        </a>
                        <ul class="dropdown-menu" role="menu">
                            <li><a href="/logout">退出</a></li>
                        </ul>
                    </li>
                <?php endif;?>
            </ul>
        </nav>
    </header>
    <!-- 顶栏 End -->
    <!-- 中间容器层 Start -->
    <div id="mat-container">

        <!-- 左侧边栏 Start -->
        <div id="mat-side" class="scrollbar-hover" style="max-height: 1000px; overflow: scroll;">
            <!-- 左侧导航菜单 Start -->
            <nav class="menu" data-toggle="menu" id="mat-nav">
                <ul class="nav">
                    <li class="header">菜单分类</li>
                    <?php
                        $menus = $this->params['menus'];
                        $curModule = $this->params['curModule'];
                        $curController = $this->params['curController'];
                        $curAction = $this->params['curAction'];
                    ?>
                    <?php foreach($menus as $moduleId => $module): ?>
                        <li class="nav-parent<?php echo $curModule == $moduleId ? ' active show' : '';?>">
                            <a href="javascript:;">
                                <i class="icon <?php echo $module['icon']; ?>"></i>
                                <span class="title"><?php echo $module['name']; ?></span>
                            </a>
                            <?php if(!empty($module['controller_menus'])):?>
                                <ul class="nav">
                                    <?php foreach($module['controller_menus'] as $controllerId => $controller):?>
                                        <li class="<?php echo $curController == $controllerId ? 'active show' : '';?>">
                                            <?php if(empty($controller['action_menus'])):?>
                                                <a href="<?php echo $controller['url']; ?>">
                                                    <span class="title"><?php echo $controller['name']; ?></span>
                                                </a>
                                            <?php elseif(count($controller['action_menus']) == 1): ?>
                                                <?php $resetAction = reset($controller['action_menus']); ?>
                                                <a href="<?php echo $resetAction['url']; ?>">
                                                    <span class="title"><?php echo $controller['name']; ?></span>
                                                </a>
                                            <?php else:?>
                                                <a href="javascript:;">
                                                    <span class="title"><?php echo $controller['name']; ?></span>
                                                </a>
                                                <ul class="nav" <?php echo $controllerId != $curController ? 'style="display:none"' : ''; ?>>
                                                    <?php foreach($controller['action_menus'] as $actionId => $action): ?>
                                                        <li style="padding-left: 42px;" class="<?php echo $controllerId==$curController && $actionId==$curAction ? 'active' : ''; ?>">
                                                            <a href="<?php echo $action['url']; ?>"><?php echo $action['name'] ?></a>
                                                        </li>
                                                    <?php endforeach;?>
                                                </ul>
                                            <?php endif;?>
                                        </li>
                                    <?php endforeach;?>
                                </ul>
                            <?php endif; ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </nav>
            <!-- 左侧导航菜单 End -->
        </div>
        <!-- 左侧边栏 End -->

        <!-- 主容器层 Start -->
        <div id="mat-main">
            <?= $content ?>
        </div>
        <!-- 主容器层 End -->

        <!-- 右侧边栏 Start -->
        <aside id="mat-aside">

        </aside>
        <!-- 右侧边栏 End -->

        <!-- 底栏 Start -->
        <footer id="mat-footer">
            <p class="pull-left"></p>
            <div class="pull-right">
                &copy;&nbsp;<?=date('Y')?> Meitu, Inc.
            </div>
        </footer>
        <!-- 底栏 End -->
    </div>
    <!-- 中间容器层 End -->
</div>
<!-- 整体布局 End -->
</body>
</html>
<?php $this->endPage() ?>
