<?php

namespace Aventura\Diary\Bookable\Availability\Timetable;

use \Aventura\Diary\DateTime\Period\PeriodInterface;

/**
 * Represents a bookable entity's default available dates and times.
 * 
 * @author Miguel Muscat <miguelmuscat93@gmail.com>
 */
interface TimetableInterface
{
    
    /**
     * Gets the rules that define this timetable.
     * 
     * @return array An array of Rule\RuleInterface instances.
     */
    public function getRules();
    
    /**
     * Checks if the given period fits in this timetable.
     * 
     * @param PeriodInterface $period The period to check
     * @return boolean True if the period obeys all of this timetable's rules, false if not.
     */
    public function isAvailable(PeriodInterface $period);
    
}
