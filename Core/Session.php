<?php

namespace App\Core;

class Session
{
    protected const FLASH_KEY = 'flash_messages';

    public function __construct()
    {
        session_start();
        $flashMessages = $_SESSION[self::FLASH_KEY] ?? [];
        foreach ($flashMessages as $key => &$flashMessage) {
            // mark to be removed
            $flashMessage['remove'] = true;
        }
        
        $_SESSION[self::FLASH_KEY] = $flashMessages;
    }

    public function setFlash($key, $message)
    {
        $_SESSION[self::FLASH_KEY][$key] = [
            'remove' => false,
            'value' => $message
        ];
    }

    public function getFlash($key)
    {
        return $_SESSION[self::FLASH_KEY][$key]['value'] ?? false;
    }

    public function __destruct()
    {
        $flashMessages = $_SESSION[self::FLASH_KEY] ?? [];
        foreach ($flashMessages as $key => &$flashMessage) {
            if ($flashMessage['remove']) {
                unset($flashMessages[$key]);
            }
        }
        $_SESSION[self::FLASH_KEY] = $flashMessages;
    }

    // set session
    public function set($key, $value)
    {
        $_SESSION[$key] = $value;
    }

    // get session
    public function get($key)
    {
        return $_SESSION[$key] ?? false;
    }

    // remove session
    public function remove($key)
    {
        unset($_SESSION[$key]);
    }

    // check if session exists
    public function exists($key)
    {
        return isset($_SESSION[$key]);
    }

    // destroy session
    public function destroy()
    {
        session_destroy();
    }

    // regenerate session
    public function regenerate()
    {
        session_regenerate_id();
    }

    // get session id
    public function getId()
    {
        return session_id();
    }

    // get session name
    public function getName()
    {
        return session_name();
    }

    // get session status
    public function getStatus()
    {
        return session_status();
    }

    // get session save path
    public function getSavePath()
    {
        return session_save_path();
    }

    // get session cookie params
    public function getCookieParams()
    {
        return session_get_cookie_params();
    }

    // set session cookie params
    public function setCookieParams($lifetime, $path, $domain, $secure, $httponly)
    {
        session_set_cookie_params($lifetime, $path, $domain, $secure, $httponly);
    }

    // get session cache limiter
    public function getCacheLimiter()
    {
        return session_cache_limiter();
    }

    // set session cache limiter
    public function setCacheLimiter($cache_limiter)
    {
        session_cache_limiter($cache_limiter);
    }
    
}
