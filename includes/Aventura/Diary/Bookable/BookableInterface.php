<?php

namespace Aventura\Diary\Bookable;

use \Aventura\Diary\Bookable\Availability\AvailabilityInterface,
    \Aventura\Diary\DateTime\Period\PeriodInterface;

/**
 * Represents an arbitrary entity that can be booked.
 * 
 * @author Miguel Muscat <miguelmuscat93@gmail.com>
 */
interface BookableInterface
{
    
    /**
     * Gets the avaialbility.
     * 
     * @return AvailabilityInterface The availability.
     */
    public function getAvailability();
    
    /**
     * Checks if a a specific period of time can be booked.
     * 
     * @param PeriodInterface $period The period to book.
     * @return boolean <b>True</b> if the period can be booked, <b>false</b> if not.
     */
    public function canBook(PeriodInterface $period);
    
    /**
     * Books a specific period of time.
     * 
     * @param PeriodInterface $period The period to book.
     * @return BookableInterface This instance.
     */
    public function book(PeriodInterface $period);
    
}
