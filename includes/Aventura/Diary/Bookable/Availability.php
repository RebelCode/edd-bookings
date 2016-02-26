<?php

namespace Aventura\Diary\Bookable;

use \Aventura\Diary\Bookable\Availability\AvailabilityAbstract;
use \Aventura\Diary\DateTime\Period\PeriodInterface;

/**
 * Availability class.
 *
 * @author Miguel Muscat <miguelmuscat93@gmail.com>
 */
class Availability extends AvailabilityAbstract
{
    
    /**
     * The bookings, as an array.
     * 
     * @var type 
     */
    protected $_bookings;

    /**
     * {@inheritdoc}
     */
    protected function _construct() {
        $this->_bookings = array();
    }

    /**
     * {@inheritdoc}
     * 
     * @param PeriodInterface $period The period to book.
     */
    protected function _addBooking(PeriodInterface $period)
    {
        $id = $this->_genBookingId($period);
        if ($id === null){
            $this->_bookings[] = $period;
        } else {
            $this->_bookings[$id] = $period;
        }
        ksort($this->_bookings, SORT_NUMERIC);
    }
    
    /**
     * Generates a booking id for storage.
     * 
     * @internal Used to generate the array key to use for a booking.
     * @param PeriodInterface $period The period for which to generate the ID.
     * @return string The generated ID.
     */
    protected function _genBookingId(PeriodInterface $period)
    {
        return $period->getStart()->getTimestamp();
    }

    /**
     * {@inheritdoc}
     * 
     * @return array The bookings, as an array of DateTimePeriod instances.
     */
    public function getBookings()
    {
        return $this->_bookings;
    }

    /**
     * {@inheritdoc}
     * 
     * @param PeriodInterface $period The booked period to remove.
     */
    protected function _removeBooking(PeriodInterface $period)
    {
        $id = $this->_genBookingId($period);
        unset($this->_bookings[$id]);
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
        return isset($this->_bookings[$id]);
    }
    
    /**
     * {@inheritdoc}
     * 
     * @param PeriodInterface $period The period to check.
     * @return boolean <b>True if the period can be booked, <b>false</b> if not.
     */
    public function doesBookingConflict(PeriodInterface $period)
    {
        // First check if the exact period is booked
        if ($this->isBooked($period)) {
            return false;
        }
        foreach ($this->getBookings() as $booking) {
            if ($period->overlaps($booking)) {
                return false;
            }
        }
        return true;
    }
    
    /**
     * {@inheritdoc}
     * 
     * @param PeriodInterface $period The period to check.
     * @return boolean <b>True</b> if the period is available, <b>false</b> if not.
     */
    public function isAvailable(PeriodInterface $period)
    {
        return $this->doesBookingConflict($period) && $this->getTimetable()->isAvailable($period);
    }

}
