<?php

/**
 * Gets the autoloader singleton instance.
 * 
 * @return Aventura\Autoloader
 */
function eddBookingsAutoloader()
{
    /* @var $instance Aventura\Autoloader */
    static $instance = null;
    // If null, instantiate
    if (is_null($instance)) {
        $className = 'Aventura\\Autoloader';
        if (!class_exists($className)) {
            $dir = dirname(__FILE__);
            $classNameAsPath = str_replace('\\', DIRECTORY_SEPARATOR, $className);
            $classPath = sprintf('%1$s%2$s%3$s.php', $dir, DIRECTORY_SEPARATOR, $classNameAsPath);
            require_once $classPath;
        }
        $instance = new $className();
        $instance->register();
    }
    return $instance;
}
