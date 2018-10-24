<?php
namespace common\models\user;

use common\models\BaseModel;

class UserModel extends BaseModel
{

    const STATUS_INACTIVE = 0; //未激活认证
    const STATUS_ENABLE = 1; //正常
    const STATUS_DISABLE = 2; //禁用
    const STATUS_DELETE = -99;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%user}}';
    }

}