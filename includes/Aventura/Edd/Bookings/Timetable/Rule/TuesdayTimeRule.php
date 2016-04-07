<?php

namespace Aventura\Edd\Bookings\Timetable\Rule;

use \Aventura\Diary\DateTime\Day;

/**
 * TuesdayTimeRule
 *
 * @author Miguel Muscat <miguelmuscat93@gmail.com>
 */
class TuesdayTimeRule extends SingleDotwTimeRule
{

    const DOTW = Day::TUESDAY;

}
