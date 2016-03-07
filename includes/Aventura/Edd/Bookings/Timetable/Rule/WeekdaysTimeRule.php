<?php

namespace Aventura\Edd\Bookings\Timetable\Rule;

use \Aventura\Diary\Bookable\Availability\Timetable\Rule\DotwTimeRangeRule;
use \Aventura\Diary\DateTime\Day;

/**
 * Description of WeekdaysTimeRule
 *
 * @author Miguel Muscat <miguelmuscat93@gmail.com>
 */
class WeekdaysTimeRule extends DotwTimeRangeRule
{
    
    public function __construct($timeLower, $timeUpper)
    {
        parent::__construct(Day::MONDAY, Day::FRIDAY, $timeLower, $timeUpper);
    }

}
