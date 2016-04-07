<?php

namespace Aventura\Edd\Bookings\Timetable\Rule;

use \Aventura\Diary\DateTime\Day;

/**
 * Description of WeekendTimerule
 *
 * @author Miguel Muscat <miguelmuscat93@gmail.com>
 */
class WeekendTimeRule extends DotwTimeRule
{
    
    public function __construct($timeLower, $timeUpper)
    {
        parent::__construct(Day::SATURDAY, Day::SUNDAY, $timeLower, $timeUpper);
    }

}
