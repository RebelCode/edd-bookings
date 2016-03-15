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
     * Flag used internally to determine if bookings have been fetched from the DB.
     * 
     * @var boolean
     */
    protected $_hasBookings;
    
    /**
     * Constructs a new instance.
     * 
     * @param integer $id The ID.
     */
    public function __construct($id)
    {
        parent::__construct();
        $this->setId($id)
                ->_setHasBookings(false);
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
     * Check if this availabiltiy has fetched bookings from the DB.
     * 
     * @return boolean
     */
    public function hasBookings()
    {
        return $this->_hasBookings;
    }
    
    /**
     * Sets the hasBookings flag.
     * 
     * @param boolean $hasBookings
     * @return Availability This instance.
     */
    protected function _setHasBookings($hasBookings)
    {
        $this->_hasBookings = $hasBookings;
        return $this;
    }
        
    /**
     * {@inheritdoc}
     * 
     * @return array The bookings, as an array of DateTimePeriod instances.
     */
    public function getBookings()
    {
        if (!$this->hasBookings()) {
            // Get bookings for this availability
            $bookings = eddBookings()->getBookingController()->getBookingsForAvailability($this->getId());
            foreach ($bookings as $booking) {
                $this->addBooking($booking);
            }
            $this->_setHasBookings(true);
        }
        return $this->_bookings;
    }
    
    /**
     * {@inheritdoc}
     * 
     * @param PeriodInterface $period The period to check.
     * @return boolean <b>True</b> if the period is booked, <b>false</b> if it's not.
     */
    public function isBooked(PeriodInterface $period)
    {
        $id = $this->_genBookingId($period);
        $bookings = $this->getBookings();
        return isset($bookings[$id]);
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
