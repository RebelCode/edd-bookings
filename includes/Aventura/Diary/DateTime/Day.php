<?php

namespace Aventura\Diary\DateTime;

use \Aventura\Diary\Utils\Enum;
use \ReflectionClass;

/**
 * Day enum.
 *
 * @author Miguel Muscat <miguelmuscat93@gmail.com>
 */
abstract class Day extends Enum
{

    const MONDAY = 1;
    const TUESDAY = 2;
    const WEDNESDAY = 3;
    const THURSDAY = 4;
    const FRIDAY = 5;
    const SATURDAY = 6;
    const SUNDAY = 7;
    
}
