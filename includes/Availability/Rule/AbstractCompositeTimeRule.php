<?php

namespace Aventura\Edd\Bookings\Availability\Rule;

use Aventura\Diary\Bookable\Availability\Timetable\Rule\TimeRangeRule;
use Aventura\Diary\DateTime;
use Aventura\Diary\DateTime\Duration;
use Aventura\Diary\DateTime\Period;
use Aventura\Diary\DateTime\Period\PeriodInterface;
use Aventura\Edd\Bookings\Availability\SessionRule\SessionRuleInterface;

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

        $sessions = array();
        $length   = $duration->getSeconds();
        $numDays  = 0;

        do {
            $currentDay = $range->getStart()->getDate()->copy()->plus(Duration::days($numDays));
            $dayStart   = $currentDay->copy()->plus($this->getLower());
            $dayEnd     = $currentDay->copy()->plus($this->getUpper());

            if ($dayEnd->isAfter($range->getEnd(), true)) {
                break;
            }

            $this->_generateSessions($dayStart->getTimestamp(), $dayEnd->getTimestamp(), $length, $sessions);

            $numDays++;
        } while(true);

        // Restore negation
        $this->setNegation($negation);

        return $sessions;
    }

    /**
     * Generates sessions of a specific length in the given range.
     *
     * @since [*next-version*]
     */
    protected function _generateSessions($rangeStart, $rangeEnd, $length, array &$results = [], array $startTimes = [])
    {
        $sessionStart = $rangeStart;
        $sessionEnd   = $sessionStart + $length;

        if ($sessionEnd > $rangeEnd) {
            return;
        }

        $session = new Period(
            new DateTime($sessionStart),
            new Duration($sessionEnd - $sessionStart)
        );

        if ($this->obeys($session)) {
            $results[$sessionStart] = $session;
            $startTimes[$rangeStart] = 1;
        }

        if (isset($startTimes[$sessionEnd])) {
            return;
        }

        $this->_generateSessions($sessionEnd, $rangeEnd, $length, $results, $startTimes);
    }

    /**
     * Generates the child rules.
     *
     * @return array
     */
    abstract public function generateChildRules();

}
