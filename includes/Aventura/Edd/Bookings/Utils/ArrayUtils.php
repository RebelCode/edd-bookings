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

}
