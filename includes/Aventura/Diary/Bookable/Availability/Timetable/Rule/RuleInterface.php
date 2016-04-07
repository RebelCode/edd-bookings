<?php

namespace Aventura\Diary\Bookable\Availability\Timetable\Rule;

use \Aventura\Diary\DateTime\Period\PeriodInterface;

/**
 * Represents a single timetable rule.
 * 
 * @author Miguel Muscat <miguelmuscat93@gmail.com>
 */
interface RuleInterface
{
    
    /**
     * Checks if a specific period obeys this rule.
     * 
     * @param PeriodInterface $period The period to check
     * @return boolean True if the period obeys this rule, false if it doesn't.
     */
    public function obeys(PeriodInterface $period);
    
}
