<?php

if (!function_exists('diaryAutoloader')) {
    /**
     * Gets the Diary library's autoloader singleton instance.
     * 
     * @return Aventura\Diary\Autoloader
     */
    function diaryAutoloader()
    {
        /* @var $instance Aventura\Diary\Autoloader */
        static $instance = null;

        if (is_null($instance)) {
            $instance = newDiaryAutoloader();
            $instance->register();
        }

        return $instance;
    }
}

if (!function_exists('newDiaryAutoloader')) {
    /**
     * Creates a new Diary autoloader instance.
     * 
     * @return Aventura\Diary\Autoloader
     */
    function newDiaryAutoloader()
    {
        $className = 'Aventura\\Diary\\Autoloader';
        if (!class_exists($className)) {
            $dir = dirname(__FILE__);
            $classPath = sprintf('%1$s/%2$s.php', $dir, str_replace('\\', DIRECTORY_SEPARATOR, $className) );
            require_once $classPath;
        }
        return new $className();
    }
}
