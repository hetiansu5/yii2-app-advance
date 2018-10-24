<?php
namespace common\constants;

/**
 * 错误码常量
 */
class ErrorCode
{
    const SUCCESS = 0;

    const INVALID_CLIENT_PARAM = 400;
    const NOT_FOUND = 404;
    const NOT_ALLOWED_METHOD = 405;
    const SYSTEM_ERROR = 500;

    const NOT_AUTH = 1000;

    //各业务的错误码，使用5位的错误码，前三位表示功能模块，如用户模块；后两位表示具体错误类型


}