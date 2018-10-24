<div class="row">
    <form class="form-horizontal" id="account-form" action="/account/edit" method="POST">
        <div class="form-group">
            <label for="field-email" class="col-sm-2 required">邮箱</label>
            <div class="col-sm-8">
                <input type="text" class="form-control" id="field-email" name="email" value="<?php echo $accountInfo['email']; ?>"
                <?php if($accountInfo): ?>readonly<?php endif; ?>/>
            </div>
        </div>
        <div class="form-group">
            <label for="field-name" class="col-sm-2">姓名</label>
            <div class="col-sm-8">
                <input type="text" class="form-control" id="field-name" name="real_name" value="<?php echo $accountInfo['real_name']; ?>"/>
            </div>
        </div>
        <div class="form-group">
            <label for="field-mobile" class="col-sm-2">密码</label>
            <div class="col-sm-8">
                <input type="text" class="form-control" id="field-pwd" name="pwd" value=""/>
            </div>
        </div>
        <div class="form-group">
            <label for="field-mobile" class="col-sm-2">联系方式</label>
            <div class="col-sm-8">
                <input type="text" class="form-control" id="field-mobile" name="mobile" value="<?php echo $accountInfo['mobile']; ?>" />
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-2">是否只读</label>
            <div class="col-sm-10">
                <div class="radio">
                    <label>
                        <input type="radio" name="readonly" value="1" <?php echo $accountInfo['is_readonly'] == '1' ? 'checked' : ''; ?> /> 是
                    </label>&nbsp;&nbsp;&nbsp;&nbsp;
                    <label>
                        <input type="radio" name="readonly" value="0" <?php echo !$accountInfo || $accountInfo['is_readonly'] == '0' ? 'checked' : ''; ?> /> 否
                    </label>
                </div>
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-2">是否管理员</label>
            <div class="col-sm-10">
                <div class="radio">
                    <label>
                        <input type="radio" name="is_manager" value="1" <?php echo $accountInfo['is_manager'] == '1' ? 'checked' : ''; ?> /> 是
                    </label>&nbsp;&nbsp;&nbsp;&nbsp;
                    <label>
                        <input type="radio" name="is_manager" value="0" <?php echo !$accountInfo || $accountInfo['is_manager'] == '0' ? 'checked' : ''; ?>/> 否
                    </label>
                    <span class="text-danger">(只有管理员才有权限设置)</span>
                </div>
            </div>
        </div>

        <div class="form-group">
            <div class="col-sm-offset-2 col-sm-10">
                <input type="hidden" name="id" value="<?php echo $accountInfo['id']; ?>" />
                <input type="hidden" name="type" value="<?php echo $accountInfo['type']; ?>" />
                <button type="submit" name="submit" value="1" class="btn btn-info">保存</button>
            </div>
        </div>
    </form>
</div>
<script>
    $(function() {
        $("#account-form").myAjaxForm();
    })
</script>