<?php

namespace LoganStellway\Base\Helper;

class Session
{
    /**
     * Get session data
     */
    public static function init()
    {
        if (!session_id()) {
            session_start();
        }
    }

    /**
     * Get session variable
     */
    public static function get(string $key, $default = null)
    {
        self::init();
        return $_SESSION[$key] ?? $default;
    }

    /**
     * Set session variable
     */
    public static function set(string $key, string $val)
    {
        self::add([$key => $val]);
    }

    /**
     * Add session data
     * 
     * @param array $data
     */
    public static function add(array $data)
    {
        self::init();

        foreach ($data as $key => $val) {
            $_SESSION[$key] = $val;
        }
    }

    /**
     * Unset session variable
     */
    public static function uns(string $key)
    {
        self::init();
        if (isset($_SESSION[$key])) {
            unset($_SESSION[$key]);
        }
    }
}
