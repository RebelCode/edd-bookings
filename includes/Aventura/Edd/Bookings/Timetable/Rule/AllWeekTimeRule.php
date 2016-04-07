<?php

namespace Aventura\Edd\Bookings\Timetable\Rule;

use \Aventura\Diary\DateTime\Day;

/**
 * Description of AllWeektimeRule
 *
 * @author Miguel Muscat <miguelmuscat93@gmail.com>
 */
class AllWeekTimeRule extends DotwTimeRule
{
    
    public function __construct($timeLower, $timeUpper)
    {
        parent::__construct(Day::MONDAY, Day::SUNDAY, $timeLower, $timeUpper);
    }

}
