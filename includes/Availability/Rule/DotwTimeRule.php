<?php

namespace Aventura\Edd\Bookings\Availability\Rule;

use \Aventura\Diary\DateTime;
use \Aventura\Diary\DateTime\Day;
use \Aventura\Diary\DateTime\Duration;

/**
 * A time rule for a day of the week.
 *
 * This class differs from SimpleDotwTimeRule in that it caters for time overflow by creating multiple
 * SimpleDotwTimeRule instances such that times can overflow into other non-obedient days of the week but
 * still match this rule.
 *
 * @author Miguel Muscat <miguelmuscat93@gmail.com>
 */
class DotwTimeRule extends AbstractCompositeTimeRule
{

    /**
     * The range's dotw, 1-based.
     *
     * @see Day
     * @var int
     */
    protected $dotw;

    /**
     * Constructs a new instance.
     */
    public function __construct($dotw, DateTime $timeLower, DateTime $timeUpper)
    {
        parent::__construct($timeLower, $timeUpper);
        $this->dotw = $dotw;
    }

    /**
     * Gets the day of the week.
     *
     * @see Day
     * @return int The day of the week (1-based).
     */
    public function getDotw()
    {
        return $this->dotw;
    }

    /**
     * Clamps a dotw index between 1 and 7.
     *
     * @param int $dotw
     * @return int
     */
    protected function clampDotw($dotw)
    {
        // Normalize negative numbers into positive range
        $nDotw = ($dotw <= 0)
            ? 7 - $dotw
            : $dotw;

        return (($nDotw - 1) % 7) + 1;
    }

    /**
     * {@inheritdoc}
     */
    public function generateChildRules()
    {
        // Pre-fetch values
        $lower = $this->getLower()->getTimestamp();
        $upper = $this->getupper()->getTimestamp();

        if ($lower < 0) {
            // Negative overflow
            return $this->generateNegativeOverflowRules();
        } else if ($upper > Duration::SECONDS_IN_DAY) {
            // Positive overflow
            return $this->generatePositiveOverflowRules();
        }

        // No overflow - replicate the parent rule
        return array(
            $this->createChildRule($this->getDotw(), $lower, $upper)
        );
    }

    /**
     * Creates a child rule.
     *
     * @param int $dotw The DOTW index.
     * @param int $lower The lower time.
     * @param int $upper The upper time.
     * @return SimpleDotwTimeRule The rule instance or null if the rule could not be created.
     */
    protected function createChildRule($dotw, $lower, $upper)
    {
        return ($lower !== $upper)
            ? new SimpleDotwTimeRule($dotw, $dotw, new DateTime($lower), new DateTime($upper))
            : null;
    }

    /**
     * Generates child rules in the event of negative time overflow.
     *
     * @return SimpleDotwTimeRule[]
     */
    protected function generateNegativeOverflowRules()
    {
        $dotw = $this->getDotw();
        $prevDotw = $this->clampDotw($dotw - 1);
        $lower = $this->getLower()->getTimestamp();
        $upper = $this->getUpper()->getTimestamp();

        if ($upper >= 0) {
            return array_filter(array(
                $this->createChildRule($prevDotw, Duration::SECONDS_IN_DAY + $lower, Duration::SECONDS_IN_DAY),
                $this->createChildRule($dotw, 0, $upper)
            ));
        } else {
            return array_filter(array(
                $this->createChildRule($prevDotw, Duration::SECONDS_IN_DAY + $lower, Duration::SECONDS_IN_DAY + $lower)
            ));
        }
    }

    /**
     * Generates child rules in the event of positive time overflow.
     *
     * @return SimpleDotwTimeRule[]
     */
    protected function generatePositiveOverflowRules()
    {
        $dotw = $this->getDotw();
        $nextDotw = $this->clampDotw($dotw + 1);
        $lower = $this->getLower()->getTimestamp();
        $upper = $this->getUpper()->getTimestamp();

        if ($lower <= Duration::SECONDS_IN_DAY) {
            return array_filter(array(
                $this->createChildRule($dotw, $lower, Duration::SECONDS_IN_DAY),
                $this->createChildRule($nextDotw, 0, $upper - Duration::SECONDS_IN_DAY)
            ));
        } else {
            return array_filter(array(
                $this->createChildRule($nextDotw, $lower - Duration::SECONDS_IN_DAY, $upper - Duration::SECONDS_IN_DAY)
            ));
        }
    }

}
