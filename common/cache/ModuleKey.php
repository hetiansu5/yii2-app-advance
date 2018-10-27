<?php
namespace common\cache;

use common\constants\Common;

/**
 * 一级前缀键名(模块)
 * @author hts
 */
class ModuleKey
{
    const USER = Common::PROJECT_KEY . 'a:'; //用户模块
    const MERCHANT = Common::PROJECT_KEY . 'm:'; //商户模块
    const ORDER = Common::PROJECT_KEY . 'o:'; //订单模块
}