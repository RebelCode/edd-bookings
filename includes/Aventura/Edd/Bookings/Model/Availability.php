<?php

namespace Aventura\Edd\Bookings\Model;

use \Aventura\Diary\Bookable\Availability as DiaryAvailability;
use \Aventura\Diary\DateTime\Duration;
use \Aventura\Diary\DateTime\Period\PeriodInterface;

/**
 * Availability model class.
 *
 * @author Miguel Muscat <miguelmuscat93@gmail.com>
 */
class Availability extends DiaryAvailability
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
     * @return integer The ID.
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
     * Generates sessions with a fixed duration for a given range.
     * 
     * @param PeriodInterface $range The range in which to generate sessions.
     * @param Duration $duration The duration of each session
     * @return array The generated sessions.
     */
    public function generateSessionsForRange(PeriodInterface $range, Duration $duration)
    {
        $sessions = $this->getTimetable()->generateSessionsForRange($range, $duration);
        foreach ($sessions as $i => $session) {
            if ($this->doesBookingConflict($session)) {
                unset($sessions[$i]);
            }
        }
        return $sessions;
    }

}
