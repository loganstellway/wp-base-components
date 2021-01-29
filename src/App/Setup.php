<?php

namespace LoganStellway\Base\App;

class Setup
{
    /**
     * Ensure WordPress is bootstrapped
     */
    public static function isReady(): bool
    {
        return defined('ABSPATH') && function_exists('is_blog_installed') &&  is_blog_installed();
    }

    /**
     * Setup the plugin
     */
    public static function setup()
    {
        if (self::isReady()) {
            new Admin\Options();
        }
    }
}
