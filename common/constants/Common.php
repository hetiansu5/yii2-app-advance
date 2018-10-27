<?php
namespace common\constants;

/**
 * 公共常量
 */
class Common
{

    const DEFAULT_PAGE_LIMIT = 20; //默认分页条目数
    const DEFAULT_PAGE_LIMIT_MAX = 100; //最大分页条目数

    //项目的前缀key，为了避免多个项目使用同一套mc和redis资源，cache和counter的key重复使用了。尽量每个项目
    const PROJECT_KEY = 'f';
}