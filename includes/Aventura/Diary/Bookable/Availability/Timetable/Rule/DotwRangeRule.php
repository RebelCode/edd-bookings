<?php

namespace Aventura\Diary\Bookable\Availability\Timetable\Rule;

use \Aventura\Diary\DateTime\Duration;
use \Aventura\Diary\DateTime\Period\PeriodInterface;

/**
 * DayOfTheWeekRule
 *
 * @author Miguel Muscat <miguelmuscat93@gmail.com>
 */
class DotwRangeRule extends RangeRuleAbstract
{

    /**
     * Constructs a new instance.
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
     * @return boolean <b>True</b> if the period obeys this rule, <b>false</b> otherwise.
     */
    public function obeys(PeriodInterface $period)
    {
        $d1 = $this->getLower();
        $d2 = $this->getUpper();
        // Check each day in the period to see if it obeys the DOTW rule
        $c = $period->getStart()->copy();
        while ($c->isBefore($period->getEnd(), true)) {
            $dotw = intval(gmdate('N', $c->getTimestamp()));
            if ($d1 <= $d2) {
                // Case when lower is smaller than upper (ex. lower: Mon, upper: Fri)
                $obeys = ($dotw > $d1 || ($this->isLowerInclusive() && $dotw === $d1)) &&
                        ($dotw < $d2 || ($this->isUpperInclusive() && $dotw === $d2));
            } else {
                // Case when lower is bigger than upper (ex. lower: Fri, upper: Mon)
                // Not dotw in the invalid range
                $obeys = !( ($dotw > $d2 || (!$this->isUpperInclusive() && $dotw === $d2)) &&
                        ($dotw < $d1 || (!$this->isLowerInclusive() && $dotw === $d1)) );
            }
            // If not in range, stop prematurely
            if (!$obeys) {
                return false;
            }
            // Increment by 1 day
            $c->plus(Duration::days(1));
        }
        return true;
    }

}
