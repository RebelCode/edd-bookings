<?php

namespace Aventura\Edd\Bookings\Timetable\Rule;

use \Aventura\Diary\DateTime\Day;

/**
 * Description of FridayTimeRule
 *
 * @author Miguel Muscat <miguelmuscat93@gmail.com>
 */
class FridayTimeRule extends SingleDotwTimeRule
{

    const DOTW = Day::FRIDAY;

}
