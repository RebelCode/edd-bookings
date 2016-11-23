<?php

namespace Aventura\Edd\Bookings\Availability\Rule;

use \Aventura\Diary\Bookable\Availability\Timetable\Rule\TimeRangeRule;
use \Aventura\Diary\DateTime\Duration;
use \Aventura\Diary\DateTime\Period;
use \Aventura\Diary\DateTime\Period\PeriodInterface;
use \Aventura\Edd\Bookings\Availability\SessionRule\SessionRuleInterface;

/**
 * This variation of a TimeRangeRule uses multiple child rules to resolve `obeys()` checks.
 *
 * A period of time will obey a composite rule if it obeys at least one of the child rules. Implementors are only
 * required to implement the `generateChildRules()` method. This class will take care of child rule caching for
 * the first `getChildRules()` call. Subsequent changes to the upper and lower value is not recommended.
 *
 * @since [*next-version*]
 */
abstract class AbstractCompositeTimeRule extends TimeRangeRule implements SessionRuleInterface
{

    /**
     * The child rules cache.
     *
     * @var array
     */
    protected $childRules = null;

    /**
     * Gets the child rules.
     *
     * @return array
     */
    public function getChildRules()
    {
        if (is_null($this->childRules)) {
            $this->childRules = $this->generateChildRules();
        }

        return $this->childRules;
    }

    /**
     * {@inheritdoc}
     */
    public function obeys(PeriodInterface $period)
    {
        foreach ($this->getChildRules() as $rule) {
            if ($rule->obeys($period)) {
                return true;
            }
        }

        return false;
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

    /**
     * Generates the child rules.
     *
     * @return array
     */
    abstract public function generateChildRules();

}
