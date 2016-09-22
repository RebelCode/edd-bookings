<?php

namespace Aventura\Edd\Bookings\Model;

use \Aventura\Diary\Bookable;
use \Aventura\Diary\Bookable\Availability\AvailabilityInterface;
use \Aventura\Diary\DateTime\Duration;
use \Aventura\Diary\DateTime\Period\PeriodInterface;

/**
 * Service
 *
 * @author Miguel Muscat <miguelmuscat93@gmail.com>
 */
class Service extends Bookable
{

    /**
     * The ID.
     * 
     * @var integer
     */
    protected $_id;

    /**
     * Bookings enabled flag.
     * 
     * @var boolean
     */
    protected $_bookingsEnabled;

    /**
     * The length of a single session in seconds.
     * 
     * @var integer
     */
    protected $_sessionLength;

    /**
     * The session unit.
     * 
     * @var string
     */
    protected $_sessionUnit;

    /**
     * The cost of a single session.
     * 
     * @var float
     */
    protected $_sessionCost;

    /**
     * The minimum number of sessions that can be booked.
     * 
     * @var integer
     */
    protected $_minSessions;

    /**
     * The maximum number of sessions that can be booked.
     * 
     * @var integer
     */
    protected $_maxSessions;

    /**
     * Whether to show output in multiviews or not.
     * 
     * @var boolean
     */
    protected $_multiViewOutput;
    
    /**
     * Whether to use customer timezone on the frontend.
     * 
     * @var boolean
     */
    protected $_useCustomerTimezone;
    
    /**
     * Constructs a new instance.
     * 
     * @param integer $id The ID of the service.
     */
    public function __construct($id)
    {
        parent::__construct();
        $this->_setId($id)
                ->setBookingsEnabled(false)
                ->setSessionLength(1)
                ->setSessionCost(0)
                ->setSessionUnit('hours')
                ->setMinSessions(1)
                ->setMaxSessions(1)
                ->setMultiViewOutput(false)
                ->setUseCustomerTimezone(false);
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
     * Gets whether or not bookings are enabled for this service.
     * 
     * @return boolean <b>True</b> if bookings are enabled, <b>false</b> otherwise.
     */
    public function getBookingsEnabled()
    {
        return $this->_bookingsEnabled;
    }

    /**
     * Gets the session length, in seconds.
     * 
     * @return integer
     */
    public function getSessionLength()
    {
        return $this->_sessionLength;
    }

    /**
     * Gets the session unit.
     * 
     * @return string
     */
    public function getSessionUnit()
    {
        return $this->_sessionUnit;
    }
    
    /**
     * Checks if the session unit is any one of the given arguments.
     * 
     * @param mixed $args,... Any number of arguments of session unit strings.
     * @return boolean True if the session unit is one of the given arguments, false if not or no arguments where given.
     */
    public function isSessionUnit($args /* $args2, ..., $argsN */)
    {
        return in_array(strtolower($this->_sessionUnit), func_get_args());
    }

    /**
     * Gets the session cost.
     * 
     * @return float
     */
    public function getSessionCost()
    {
        return $this->_sessionCost;
    }

    /**
     * Gets the minimum number of bookable sessions.
     * 
     * @return integer
     */
    public function getMinSessions()
    {
        return $this->_minSessions;
    }

    /**
     * Gets the maximum number of bookable sessions.
     * 
     * @return integer
     */
    public function getMaxSessions()
    {
        return $this->_maxSessions;
    }

    /**
     * Gets whether to output on multi views.
     * 
     * @return boolean
     */
    public function getMultiViewOutput()
    {
        return $this->_multiViewOutput;
    }
    
    /**
     * Gets whether to use customer timezone on the frontend or not.
     * 
     * @return boolean
     */
    public function getUseCustomerTimezone()
    {
        return $this->_useCustomerTimezone;
    }
        
    /**
     * Sets the ID.
     * 
     * @param integer $id
     * @return Service
     */
    protected function _setId($id)
    {
        $this->_id = $id;
        return $this;
    }

    /**
     * Sets whether bookings for this service are enabled or not.
     * 
     * @param boolean $bookingsEnabled
     * @return Service
     */
    public function setBookingsEnabled($bookingsEnabled)
    {
        $this->_bookingsEnabled = $bookingsEnabled;
        return $this;
    }

    /**
     * Sets the session length, in seconds.
     * 
     * @param integer $sessionLength
     * @return Service
     */
    public function setSessionLength($sessionLength)
    {
        $this->_sessionLength = $sessionLength;
        return $this;
    }

    /**
     * Sets the session unit.
     * 
     * @param string The session unit
     * @return Service
     */
    public function setSessionUnit($sessionUnit)
    {
        $this->_sessionUnit = $sessionUnit;
        return $this;
    }

    /**
     * Sets the session cost.
     * 
     * @param float $sessionCost
     * @return Service
     */
    public function setSessionCost($sessionCost)
    {
        $this->_sessionCost = $sessionCost;
        return $this;
    }

    /**
     * Sets the minimum number of bookable sessions.
     * 
     * @param integer $minSessions
     * @return Service
     */
    public function setMinSessions($minSessions)
    {
        $this->_minSessions = $minSessions;
        return $this;
    }

    /**
     * Sets the maximum number of bookable sessions.
     * 
     * @param integer $maxSessions
     * @return Service
     */
    public function setMaxSessions($maxSessions)
    {
        $this->_maxSessions = $maxSessions;
        return $this;
    }

    /**
     * Gets the computed minimum session length in seconds.
     * 
     * @return integer
     */
    public function getMinSessionLength()
    {
        return $this->getMinSessions() * $this->getSessionLength();
    }

    /**
     * Gets the computed maximum session length in seconds.
     * 
     * @return integer
     */
    public function getMaxSessionLength()
    {
        return $this->getMaxSessions() * $this->getSessionLength();
    }

    /**
     * Sets whether to output on multi views.
     * 
     * @param boolean $multiViewOutput
     * @return Service
     */
    public function setMultiViewOutput($multiViewOutput)
    {
        $this->_multiViewOutput = $multiViewOutput;
        return $this;
    }
    
    /**
     * Sets whether to use customer timezone on the frontend.
     * 
     * @param boolean $useCustomerTimezone True to use customer timezone on the frontend, false to not.
     * @return Service This instance.
     */
    public function setUseCustomerTimezone($useCustomerTimezone)
    {
        $this->_useCustomerTimezone = $useCustomerTimezone;
        return $this;
    }
        
    /**
     * Gets the schedule.
     * 
     * @return Schedule The schedule.
     */
    public function getSchedule()
    {
        return $this->getAvailability();
    }
    
    /**
     * Sets the schedule.
     * 
     * @param AvailabilityInterface $schedule The schedule.
     * @return Bookable This instance.
     */
    public function setSchedule(AvailabilityInterface $schedule)
    {
        return $this->setAvailability($schedule);
    }
    
    /**
     * {@inheritdoc}
     * 
     * This method also checks if the given booking obeys the session length and min-max session range.
     * 
     * @param PeriodInterface $booking The booking to check.
     * @return boolean <b>True</b> if the booking can be booked, <b>false</b> otherwise.
     */
    public function canBook(PeriodInterface $booking)
    {
        $duration = $booking->getDuration()->getSeconds();
        $min = $this->getMinSessionLength();
        $max = $this->getMaxSessionLength();
        return $duration >= $min && $duration <= $max && parent::canBook($booking);
    }

    /**
     * Generates sessions for a given range.
     * 
     * @param PeriodInterface $range The range.
     * @return array The sessions as an array of PeriodInterface instances.
     */
    public function generateSessionsForRange(PeriodInterface $range)
    {
        $singleDay = Duration::days(1, false);
        $minSessionLength = min($this->getMinSessionLength(), $singleDay);
        return $this->getSchedule()->generateSessionsForRange($range, new Duration($minSessionLength));
    }

}
