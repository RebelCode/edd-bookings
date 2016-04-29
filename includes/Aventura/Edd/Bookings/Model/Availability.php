<?php

namespace Aventura\Edd\Bookings\Model;

use \Aventura\Diary\Bookable\Availability\Timetable as DiaryTimetable;
use \Aventura\Diary\Bookable\Availability\Timetable\Rule\RangeRuleAbstract;
use \Aventura\Diary\DateTime\Duration;
use \Aventura\Diary\DateTime\Period\PeriodInterface;
use \Aventura\Edd\Bookings\Availability\SessionRule\SessionRuleInterface;

/**
 * Availability model class.
 *
 * @author Miguel Muscat <miguelmuscat93@gmail.com>
 */
class Availability extends DiaryTimetable
{

    /**
     * The ID.
     * 
     * @var integer
     */
    protected $_id;
    
    /**
     * Constructs a new instance.
     * 
     * @param integer $id The ID.
     */
    public function __construct($id)
    {
        parent::__construct();
        $this->setId($id);
    }

    /**
     * Gets the ID.
     * 
     * @return integer
     */
    public function getId()
    {
        return $this->_id;
    }

    /**
     * Sets the ID.
     * 
     * @param integer $id The ID.
     * @return Availability This instance.
     */
    public function setId($id)
    {
        $this->_id = $id;
        return $this;
    }

    /**
     * Generates sessions with a fixed duration for a given range, using all the rules in this availability.
     * 
     * @param PeriodInterface $range The range in which to generate sessions.
     * @param Duration $duration The duration of each session
     * @return array The generated sessions.
     */
    public function generateSessionsForRange(PeriodInterface $range, Duration $duration)
    {
        $sessions = array();
        foreach($this->getRules() as $rule) {
            if ($rule instanceof SessionRuleInterface && $rule instanceof RangeRuleAbstract) {
                $ruleSessions = $rule->generateSessionsForRange($range, $duration);
                $sessions = $rule->isNegated()
                        ? array_diff_key($sessions, $ruleSessions)
                        : array_unique($sessions + $ruleSessions);
            }
        }
        uksort($sessions, function($a, $b) {
            return intval($a) - intval($b);
        });
        return $sessions;
    }

}
