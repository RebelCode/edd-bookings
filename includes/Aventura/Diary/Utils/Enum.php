<?php

namespace Aventura\Diary\Utils;

/**
 * Description of Enum
 *
 * @author Miguel Muscat <miguelmuscat93@gmail.com>
 */
abstract class Enum
{
    
    /**
     * Gets all of the constants.
     * 
     * @return array An assoc array with constant names as array keys and their integer values as array values.
     */
    public static function getAll()
    {
        $refClass = new \ReflectionClass(get_called_class());
        return $refClass->getConstants();
    }

}
