<?php

namespace Aventura\Diary\Bookable\Availability\Timetable\Rule;

use \Aventura\Diary\DateTime\Period\PeriodInterface;
use \Exception;

/**
 * WeekNumRangeRule
 *
 * @author Miguel Muscat <miguelmuscat93@gmail.com>
 */
class WeekNumRangeRule extends RangeRuleAbstract
{

    /**
     * Constructs a new instance.
     * 
     * @param integer $lower The range starting week number.
     * @param integer $upper The range ending week number.
     */
    public function __construct($lower, $upper)
    {
        $this->setLower($lower)
                ->setUpper($upper)
                ->setLowerInclusive(true)
                ->setUpperInclusive(true)
                ->setNegation(false);
    }
    
    /**
     * {@inheritdoc}
     * 
     * @param PeriodInterface $period The period to check
     * @return boolean <b>True</b> if the period obeys the rule, <b>false</b> if not.
     * @throws Exception If the range lower and upper values are not integers.
     */
    protected function _obeys(PeriodInterface $period)
    {
        // Ensure that range values are DateTimeInterface instances.
        if (!is_int($this->getLower()) || !is_int($this->getUpper()) ) {
            throw new Exception('Invalid range values! WeekNumRangeRule must have integer range values.');
        }
        
        // Prepare vars
        $lower = $this->getLower();
        $upper = $this->getUpper();
        $periodStartWeek = intval($period->getStart()->format('W'));
        $periodEndWeek = intval($period->getEnd()->format('W'));
        
        // Check if the period is in the range
        $inRange = ($periodStartWeek > $lower || ($this->isLowerInclusive() && $periodStartWeek === $lower)) &&
                ($periodEndWeek < $upper || ($this->isUpperInclusive() && $periodEndWeek === $upper));
        
        // If the start week is before the end week, the period must also be on the same year
        if ($lower < $upper) {
            return $inRange && intval($period->getStart()->format('Y')) === intval($period->getEnd()->format('Y'));
        }
        
        // Otherwise, if the end week is before the start week, the initial inRange check will suffice
        return $inRange;
    }

}
