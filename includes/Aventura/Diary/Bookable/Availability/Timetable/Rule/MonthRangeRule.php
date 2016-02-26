<?php

namespace Aventura\Diary\Bookable\Availability\Timetable\Rule;

use \Aventura\Diary\DateTime\Period\PeriodInterface;

/**
 * Description of MonthRangeRule
 *
 * @author Miguel Muscat <miguelmuscat93@gmail.com>
 */
class MonthRangeRule extends RangeRuleAbstract
{
    
    /**
     * Constructs a new instance.
     * 
     * @param integer $lower The range's starting month.
     * @param integer $upper The range's ending month.
     */
    public function __construct($lower, $upper)
    {
        $this->setLower($lower)
                ->setUpper($upper)
                ->setLowerInclusive(true)
                ->setUpperInclusive(true);
    }

    /**
     * {@inheritdoc}
     * 
     * @param PeriodInterface $period The period to check.
     * @return boolean <b>True</b> if the period obeys the rule, <b>false</b> otherwise.
     */
    public function obeys(PeriodInterface $period)
    {
        $m1 = $this->getLower();
        $m2 = $this->getUpper();
        // Check each day in the period to see if it obeys the month rule
        $c = $period->getStart()->copy();
        while ($c->isBefore($period->getEnd(), true)) {
            $month = intval(gmdate('n', $c->getTimestamp()));
            if ($m1 <= $m2) {
                // Case when lower is smaller than upper (ex. lower: Jan, upper: May)
                $obeys = ($month > $m1 || ($this->isLowerInclusive() && $month === $m1)) &&
                        ($month < $m2 || ($this->isUpperInclusive() && $month === $m2));
            } else {
                // Case when lower is bigger than upper (ex. lower: May, upper: Jan)
                // Not month in the invalid range
                $obeys = !( ($month > $m2 || (!$this->isUpperInclusive() && $month === $m2)) &&
                        ($month < $m1 || (!$this->isLowerInclusive() && $month === $m1)) );
            }
            // If not in range, stop prematurely
            if (!$obeys) {
                return false;
            }
            // Increment by 1 month
            $c->plus(Duration::months(1));
        }
        return true;
    }

}
