<?php

namespace Aventura\Diary\Bookable\Availability;

use \Aventura\Diary\Bookable\Availability\Timetable\TimetableInterface;
use \Aventura\Diary\DateTime\Period\PeriodInterface;

/**
 * Represents an authorative and conceptual object that handles a bookable entity's availability.
 * 
 * @author Miguel Muscat <miguelmuscat93@gmail.com>
 */
interface AvailabilityInterface
{
    
    /**
     * Gets the timetable for this availability.
     * 
     * @return TimetableInterface The timetable instance.
     */
    public function getTimetable();
    
    /**
     * Gets the booked times in this availability.
     * 
     * @return array An array of \Aventura\Diary\Utils\DateTimePeriod instances.
     */
    public function getBookings();
    
    /**
     * Adds a booked period to this availability.
     * 
     * @param PeriodInterface $period The period to book.
     * @return boolean <b>True</b> if the booking was added successfully, <b>false</b> on failure.
     */
    public function addBooking(PeriodInterface $period);
    
    /**
     * Removes a booked period from this availability if it exists.
     * 
     * @param PeriodInterface $period The booked period to remove.
     */
    public function removeBooking(PeriodInterface $period);
    
    /**
     * Checks if an exact period is booked.
     * 
     * @param PeriodInterface $period The booked period to check.
     * @return boolean <b>True</b> if the period is booked, <b>false</b> otherwise.
     */
    public function isBooked(PeriodInterface $period);
    
    /**
     * Checks if a booking conflicts or overlap with a specific period.
     * 
     * @param PeriodInterface $period The period to check.
     * @return boolean <b>True</b> if the period conflicts with a booking, <b>false</b> otherwise.
     */
    public function doesBookingConflict(PeriodInterface $period);
    
    /**
     * Checks if a particular period is available for booking.
     * 
     * @param PeriodInterface $period The period to check
     * @return boolean True if the period is available, false if not.
     */
    public function isAvailable(PeriodInterface $period);
    
}
