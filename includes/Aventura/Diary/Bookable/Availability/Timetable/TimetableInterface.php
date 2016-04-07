<?php

namespace Aventura\Diary\Bookable\Availability\Timetable;

use \Aventura\Diary\Bookable\Availability\Timetable\Rule\RuleInterface;
use \Aventura\Diary\DateTime\Period\PeriodInterface;

/**
 * Represents a bookable entity's default available dates and times.
 * 
 * @author Miguel Muscat <miguelmuscat93@gmail.com>
 */
interface TimetableInterface
{
    
    /**
     * Adds a rule.
     * 
     * @param RuleInterface $rule The rule to add.
     * @return TimetableAbstract This instance.
     */
    public function addRule(RuleInterface $rule);
    
    /**
     * Checks if this timetable has a rule at a specific index.
     * 
     * @param integer $index The index to check.
     * @return boolean <b>True</b> if the timetable has a rule at the given index, <b>false</b> otherwise.
     */
    public function hasRule($index);
    
    /**
     * Gets the rule at a specific index.
     * 
     * @param integer $index The index of the rule to retrieve.
     * @return RuleInterface The rule at the given index or <b>null</b> if no rule exists at the given index.
     */
    public function getRule($index);
    
    /**
     * Removes the rule at a specific index.
     * 
     * @param integer $index The index of the rule to remove.
     * @return TimetableAbstract This instance.
     */
    public function removeRule($index);
    
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
