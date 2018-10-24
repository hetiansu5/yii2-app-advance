<?php
namespace common\cache;

/**
 * 二级前缀键名(功能、对象)
 * @author hts
 */
class UserModuleKey
{
    const MODULE_KEY = ModuleKey::USER;

    const GROUP_INFO = self::MODULE_KEY . 'g:';
    const USER_INFO = self::MODULE_KEY . 'u:';

}