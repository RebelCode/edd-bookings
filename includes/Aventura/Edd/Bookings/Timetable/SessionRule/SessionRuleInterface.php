<?php

namespace Aventura\Edd\Bookings\Timetable\SessionRule;

use \Aventura\Diary\DateTime\Duration;
use \Aventura\Diary\DateTime\Period\PeriodInterface;

/**
 * Custom RuleAbstract class.
 *
 * @author Miguel Muscat <miguelmuscat93@gmail.com>
 */
interface SessionRuleInterface
{
    
    /**
     * Generates a set of sessions that match this rule, for the given range.
     * 
     * @param PeriodInterface $range The range for which to generate the sessions.
     * @param Duration $duration The duration of each session.
     * @return array An array of PeriodInterface instances that lie inside the range and obey this rule.
     */
    public function generateSessionsForRange(PeriodInterface $range, Duration $duration);

}
