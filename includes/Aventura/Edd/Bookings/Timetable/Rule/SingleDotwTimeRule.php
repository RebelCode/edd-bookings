<?php

namespace Aventura\Edd\Bookings\Timetable\Rule;

use \Aventura\Diary\Bookable\Availability\Timetable\Rule\DotwTimeRangeRule;
use \Aventura\Diary\DateTime;

/**
 * Description of TuesdayTimeRule
 *
 * @author Miguel Muscat <miguelmuscat93@gmail.com>
 */
class SingleDotwTimeRule extends DotwTimeRangeRule
{

    /**
     * Day of the week index.
     */
    const DOTW = 0;

    /**
     * {@inheritdoc}
     * 
     * @param DateTime $timeLower The range lower datetime.
     * @param DateTime $timeUpper The range upper datetime.
     */
    public function __construct($timeLower, $timeUpper)
    {
        parent::__construct(static::DOTW, static::DOTW, $timeLower, $timeUpper);
    }

}