<?php

namespace Aventura\Diary\Bookable\Availability;

use \Aventura\Diary\Bookable\Availability\Timetable\TimetableInterface;
use \Aventura\Diary\DateTime\Period\PeriodInterface;

/**
 * AvailabilityAbstract
 *
 * @author Miguel Muscat <miguelmuscat93@gmail.com>
 */
abstract class AvailabilityAbstract implements AvailabilityInterface
{
    
    /**
     * The timetable insance.
     * 
     * @var TimetableInterface
     */
    protected $_timetable;
    
    /**
     * Constructs a new instance.
     */
    public function __construct()
    {
        $this->setTimetable(new Timetable)
            ->_construct();
    }
    
    /**
     * Internal abstract constructor.
     * 
     * @internal Used for extending classes.
     */
    abstract protected function _construct();
    
    /**
     * Gets the timetable.
     * 
     * @return TimetableInterface The timetable.
     */
    public function getTimetable()
    {
        return $this->_timetable;
    }

    /**
     * Sets the timetable.
     * 
     * @param TimetableInterface $timetable
     * @return AvailabilityAbstract
     */
    public function setTimetable(TimetableInterface $timetable)
    {
        $this->_timetable = $timetable;
        return $this;
    }
    
    /**
     * {@inheritdoc}
     * 
     * @param PeriodInterface $period The period to book.
     * @return boolean <b>True</b> if the booking was added successfully, <b>false</b> on failure.
     */
    public function addBooking(PeriodInterface $period)
    {
        if ($this->isAvailable($period)) {
            $this->_addBooking($period);
            return true;
        }
        return false;
    }
    
    /**
     * {@inheritdoc}
     * 
     * @param PeriodInterface $period The booked period to remove.
     * @return boolean <b>True</b> if the period was found and removed, <b>false</b> otherwise.
     */
    public function removeBooking(PeriodInterface $period)
    {
        if ($this->isBooked($period)) {
            $this->_removeBooking($period);
            return true;
        }
        return false;
    }
    
    /**
     * Adds a booking.
     * 
     * @internal Used for overriding in extending classes.
     * @param PeriodInterface $period The period to book.
     */
    abstract protected function _addBooking(PeriodInterface $period);
    
    /**
     * Removes a booking.
     * 
     * @internal Used for overriding in extending classes.
     * @param PeriodInterface $period The period to find and remove.
     */
    abstract protected function _removeBooking(PeriodInterface $period);
    
}
