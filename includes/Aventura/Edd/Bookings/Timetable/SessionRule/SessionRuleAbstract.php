<?php

namespace Aventura\Edd\Bookings\Timetable\SessionRule;

use \Aventura\Diary\DateTime\Duration;
use \Aventura\Diary\DateTime\Period\PeriodInterface;

/**
 * Description of SessionRuleAbstract
 *
 * @author Miguel Muscat <miguelmuscat93@gmail.com>
 */
class SessionRuleAbstract implements SessionRuleInterface
{

    /**
     * {@inheritdoc}
     */
    public static function generateSessionsForRange(PeriodInterface $range, Duration $duration)
    {
        
    }

}
