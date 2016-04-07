<?php

namespace Aventura\Diary\Bookable\Availability\Timetable\Rule;

use \Aventura\Diary\DateTime\DateTimeInterface;
use \Aventura\Diary\DateTime\Period\PeriodInterface;

/**
 * TimeRangeRule
 *
 * @author Miguel Muscat <miguelmuscat93@gmail.com>
 */
class TimeRangeRule extends RangeRuleAbstract
{
    
    /**
     * Constructs a new instance.
     * 
     * @param DateTimeInterface $startTime
     * @param DateTimeInterface $endTime
     */
    public function __construct(DateTimeInterface $startTime, DateTimeInterface $endTime)
    {
        $this->setLower($startTime)
                ->setUpper($endTime)
                ->setLowerInclusive(true)
                ->setUpperInclusive(false)
                ->setNegation(false);
    }
        
    /**
     * {@inheritdoc}
     * 
     * @param PeriodInterface $period The period to check.
     * @return boolean <b>True</b> if the period obeys the rule, <b>false</b> otherwise.
     * @throws Exception If the range lower and upper values are not DateTimeInterface instances.
     */
    protected function _obeys(PeriodInterface $period)
    {
        // Ensure that range values are DateTimeInterface instances.
        $dateTimeInterface = 'Aventura\Diary\DateTime\DateTimeInterface';
        if (!is_a($this->getLower(), $dateTimeInterface) || !is_a($this->getUpper(), $dateTimeInterface) ) {
            throw new Exception('Invalid range values! TimeRangeRule must have DateTimeInterface range values.');
        }

        // Prepare vars
        $lower = $this->getLower();
        $upper = $this->getUpper();
        $periodStart = $period->getStart();
        $periodEnd = $period->getEnd();
        
        // Check if the period is in the range
        $inRange = $periodStart->getTime()->isAfter($lower, $this->isLowerInclusive()) &&
                $periodEnd->getTime()->isBefore($upper, $this->isUpperInclusive());
        
        // If the start time is before the end time, the period must also be on the same day
        if ($lower->isBefore($upper)) {
            return $inRange && $periodStart->getDate()->isEqualTo($periodEnd->getDate());
        }
        
        // Otherwise, if the end time is before the start time, the initial inRange check will suffice
        return $inRange;
    }

}
