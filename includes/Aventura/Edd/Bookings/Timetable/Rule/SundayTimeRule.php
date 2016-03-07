<?php

namespace Aventura\Edd\Bookings\Timetable\Rule;

use \Aventura\Diary\DateTime\Day;

/**
 * Description of SundayTimeRule
 *
 * @author Miguel Muscat <miguelmuscat93@gmail.com>
 */
class SundayTimeRule extends SingleDotwTimeRule
{

    const DOTW = Day::SUNDAY;

}
