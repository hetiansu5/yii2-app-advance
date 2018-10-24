<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="zh-cn">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=Edge,chrome=1">
    <meta name="renderer" content="webkit" />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <meta name="keywords" content="" />
    <meta name="description" content="" />
    <link rel="stylesheet" href="/common/zui/1.7.0/dist/css/zui.min.css">
    <link rel="stylesheet" href="/common/mat/mat.min.css">
    <!--    <link rel="shortcut icon" href="" type="image/x-icon" />-->
    <title>登录</title>
    <style>
        .mat-single-page .panel-login [class*=col-] {
            float:left;
        }
    </style>
</head>
<body>
<!-- 整体布局 Start -->
<div id="mat-layout">
    <div class="container-fluid mat-single-page mat-single-page-bg">
        <div class="row">
            <div class="col-xs-9 col-sm-6 col-md-4 col-lg-4">
                <div class="panel">
                    <div class="panel-body panel-login" style="padding-top: 10px;">
                        <form action="/login/auth" method="post" id="login_form" style="margin-top:0;">
                            <h3>管理后台</h3>
                            <div class="form-group">
                                <input type="text" name="username" id='id_username' class="form-control"
                                       placeholder="用户名">
                            </div>
                            <div class="form-group">
                                <input type="password" name="password" id='id_password' class="form-control"
                                       placeholder="密码">
                            </div>
                            <div class="form-group">
                                <div class="col-xs-8" style="padding-left:0">
                                    <input type="text" name="verify_code" id="id_verify_code"
                                           class="form-control verify-input" placeholder="验证码">
                                </div>
                                <div class="col-xs-4" style="padding-right:0">
                                    <img class="verify-pic" id="img_verify_code"
                                         src="/login/verify-code?t=<?= time(); ?>"
                                         onclick="this.src='/login/verify-code?t='+Math.random();"
                                         style="height:34px;cursor:pointer;"/>
                                </div>
                                <div class="clearfix"></div>
                            </div>
                            <div class="form-group">
                                <button id="id_login" class="btn btn-primary btn-block">登录</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- 整体布局 End -->
<script src="/common/mat/mat.min.js"></script>
<script src="/js/jquery.form.js"></script>
<script src="/js/admin.js"></script>
<script>
    $(function() {
        // 初始化 MAT
        window.$MAT = MAT.init();

        $("#login_form").myAjaxForm({
            success: function (resp) {
                if (resp.code != undefined) {
                    if (resp.code == 0) {
                        window.location.href = "/";
                    } else if (resp.msg != undefined && resp.msg) {
                        $("#img_verify_code").trigger("click");
                        alert(resp.msg);
                    } else {
                        $("#img_verify_code").trigger("click");
                        alert('操作失败');
                    }
                } else {
                    alert('返回结果异常');
                }
            }
        });
    });
</script>
</body>
</html>
<?php $this->endPage() ?>