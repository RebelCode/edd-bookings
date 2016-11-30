<?php

namespace Aventura\Edd\Bookings\Availability\Rule;

use \Aventura\Diary\DateTime\Day;

/**
 * Description of AllWeektimeRule
 *
 * @author Miguel Muscat <miguelmuscat93@gmail.com>
 */
class AllWeekTimeRule extends CompositeDotwTimeRule
{
    
    public function __construct($timeLower, $timeUpper)
    {
        parent::__construct(Day::MONDAY, Day::SUNDAY, $timeLower, $timeUpper);
    }

}
