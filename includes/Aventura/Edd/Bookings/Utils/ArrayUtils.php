<?php

namespace Aventura\Edd\Bookings\Utils;

/**
 * Description of ArrayUtils
 *
 * @author Miguel Muscat <miguelmuscat93@gmail.com>
 */
class ArrayUtils
{

    public static function mergeRecursiveDistinct(array &$array1, array &$array2)
    {
        $merged = $array1;
        foreach ($array2 as $key => &$value) {
            if (is_array($value) && isset($merged [$key]) && is_array($merged [$key])) {
                $merged [$key] = self::mergeRecursiveDistinct($merged [$key], $value);
            } else {
                $merged [$key] = $value;
            }
        }
        return $merged;
    }

    /**
     * Polyfill for array column function.
     *
     * @param array $array The array
     * @param string $columnName The key column name.
     * @return array
     */
    public static function arrayColumn(arrayy $array, $columnName)
    {
        if (function_exists('array_column')) {
            return array_column($array, $columnName);
        }
        return array_map(function($element) use ($columnName){
            return $element[$columnName];
        }, $array);
    }

}
