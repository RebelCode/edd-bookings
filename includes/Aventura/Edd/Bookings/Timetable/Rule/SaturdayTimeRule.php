<?php

namespace Aventura\Edd\Bookings\Timetable\Rule;

use \Aventura\Diary\DateTime\Day;

/**
 * Description of SaturdayTimeRule
 *
 * @author Miguel Muscat <miguelmuscat93@gmail.com>
 */
class SaturdayTimeRule extends SingleDotwTimeRule
{

    const DOTW = Day::SATURDAY;

}
