<?php

namespace Aventura\Edd\Bookings\Timetable\Rule;

use \Aventura\Diary\DateTime;
use \Aventura\Edd\Bookings\Timetable\SessionRule\SessionRuleInterface;

/**
 * Description of TuesdayTimeRule
 *
 * @author Miguel Muscat <miguelmuscat93@gmail.com>
 */
class SingleDotwTimeRule extends DotwTimeRule implements SessionRuleInterface
{

    /**
     * Day of the week index.
     */
    const DOTW = 0;
    
    /**
     * {@inheritdoc}
     * 
     * @param integer $dotw The range day of the week index.
     * @param DateTime $timeLower The range lower datetime.
     * @param DateTime $timeUpper The range upper datetime.
     */
    public function __construct($timeLower, $timeUpper)
    {
        parent::__construct(static::DOTW, static::DOTW, $timeLower, $timeUpper);
    }

}
