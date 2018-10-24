<?php
namespace common\lib\fw;

class Session
{
    use InstanceTrait {
        getInstance as _getInstance;
    }

    public static function getInstance($config = [])
    {
        return self::_getInstance($config);
    }

    private function __construct($config = [])
    {
        $saveHandler = isset($config['save_handler']) ? $config['save_handler'] : 'files';
        $savePath = isset($config['save_path']) ? $config['save_path'] : '/tmp';
        $name = isset($config['name']) ? $config['name'] : '';
        $gcMaxLifetime = isset($config['gc_maxlifetime']) ? $config['gc_maxlifetime'] : 0;

        $cookieParams = session_get_cookie_params();
        $cookieLifetime = isset($config['cookie_lifetime']) ? $config['cookie_lifetime'] : (isset($cookieParams['lifetime']) ? $cookieParams['lifetime'] : 0);
        $cookiePath = isset($config['cookie_path']) ? $config['cookie_path'] : (isset($cookieParams['path']) ? $cookieParams['path'] : '/');
        $cookieDomain = isset($config['cookie_domain']) ? $config['cookie_domain'] : (isset($cookieParams['domain']) ? $cookieParams['domain'] : null);
        $cookieSecure = isset($config['cookie_secure']) ? (bool)$config['cookie_secure'] : (isset($cookieParams['secure']) ? $cookieParams['secure'] : false);
        $cookieHttpOnly = isset($config['cookie_httponly']) ? (bool)$config['cookie_httponly'] : (isset($cookieParams['httponly']) ? $cookieParams['httponly'] : false);

        if ($saveHandler) {
            ini_set('session.save_handler', $saveHandler);
        }
        if ($savePath) {
            session_save_path($savePath);
        }
        if ($name) {
            session_name($name);
        }
        if ($gcMaxLifetime) {
            ini_set('session.gc_maxlifetime', $gcMaxLifetime);
        }

        session_set_cookie_params($cookieLifetime, $cookiePath, $cookieDomain, $cookieSecure, $cookieHttpOnly);
        if (!isset($_SESSION)) {
            session_start();
        }
    }

    public function close()
    {
        session_write_close();
    }

    public function destroy()
    {
        session_destroy();
    }

    public function get($key)
    {
        return isset($_SESSION[$key]) ? $_SESSION[$key] : null;
    }

    public function getAll()
    {
        return $_SESSION;
    }

    public function set($key, $value)
    {
        $_SESSION[$key] = $value;
    }

    public function delete($key)
    {
        unset($_SESSION[$key]);
    }

    public function getId()
    {
        return session_id();
    }

    public function setId($sessionId)
    {
        session_id($sessionId);
    }
}