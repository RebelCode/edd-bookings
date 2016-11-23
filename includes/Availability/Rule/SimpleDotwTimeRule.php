<?php

namespace Aventura\Edd\Bookings\Availability\Rule;

use \Aventura\Diary\Bookable\Availability\Timetable\Rule\DotwTimeRangeRule;
use \Aventura\Diary\DateTime;
use \Aventura\Diary\DateTime\Duration;
use \Aventura\Diary\DateTime\Period;
use \Aventura\Diary\DateTime\Period\PeriodInterface;
use \Aventura\Edd\Bookings\Availability\SessionRule\SessionRuleInterface;

/**
 * A rule for matching a day of the week with a specific time range.
 *
 * Unlike DotwTimeRule, this rule does not cater for times that over flow into other days of the week, which
 * could potentially result in unwanted behaviour, especially when the time values are timezone shifted.
 *
 * @since [*next-version*]
 */
class SimpleDotwTimeRule extends DotwTimeRangeRule implements SessionRuleInterface
{

    /**
     * {@inheritdoc}
     *
     * @param integer $dotwLower The range lower day of the week index.
     * @param integer $dotwUpper The range upper day of the week index.
     * @param DateTime $timeLower The range lower datetime.
     * @param DateTime $timeUpper The range upper datetime.
     */
    public function __construct($dotwLower, $dotwUpper, $timeLower, $timeUpper)
    {
        parent::__construct($dotwLower, $dotwUpper, $timeLower, $timeUpper);
    }

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
        $date = $range->getStart()->getDate();
        // Array to build and return
        $sessions = array();
        // Create first session
        $current = new Period($date->copy()->plus($this->getLower()), $duration);
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
