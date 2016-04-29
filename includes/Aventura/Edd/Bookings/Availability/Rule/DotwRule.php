<?php

namespace Aventura\Edd\Bookings\Availability\Rule;

use \Aventura\Diary\Bookable\Availability\Timetable\Rule\DotwRangeRule;
use \Aventura\Diary\DateTime\Duration;
use \Aventura\Diary\DateTime\Period;
use \Aventura\Diary\DateTime\Period\PeriodInterface;
use \Aventura\Edd\Bookings\Availability\SessionRule\SessionRuleInterface;

/**
 * Description of DotwRule
 *
 * @author Miguel Muscat <miguelmuscat93@gmail.com>
 */
class DotwRule extends DotwRangeRule implements SessionRuleInterface
{

    /**
     * {@inheritdoc}
     */
    public function generateSessionsForRange(PeriodInterface $range, Duration $duration)
    {
        // Save negation setting and disable it temporarily
        $negation = $this->isNegated();
        $this->setNegation(false);
        // Get range info
        $start = $range->getStart();
        $end = $range->getEnd();
        // Array to build and return
        $sessions = array();
        // Create first session, using only the range start's date (at 00:00)
        $current = new Period($start->copy(), $duration);
        // Iterate until the current period is before the range end
        while ($current->getEnd()->isBefore($end, true)) {
            // If the current period is inside the range and obeys this rule
            if ($current->getStart()->isAfter($start, true) && $this->obeys($current)) {
                // Add it to the resulting array
                $sessions[$current->getStart()->getTimestamp()] = $current->copy();
            }
            // Increment by the given duration
            $current->getStart()->plus($duration);
        }
        // Restore negation
        $this->setNegation($negation);
        // Return results
        return $sessions;
    }

}
