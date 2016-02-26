<?php

namespace Aventura\Diary\Bookable;

use \Aventura\Diary\DateTime\Period\PeriodInterface;

/**
 * Base implementation of a bookable entity.
 *
 * @author Miguel Muscat <miguelmuscat93@gmail.com>
 */
abstract class BookableAbstract implements BookableInterface
{
    
    /**
     * {@inheritdoc}
     * 
     * @param PeriodInterface $period The period to book.
     * @return boolean <b>True</b> if the period can be booked, <b>false</b> if not.
     */
    public function canBook(PeriodInterface $period)
    {
        return $this->getAvailability()->isAvailable($period);
    }
    
    /**
     * {@inheritdoc}
     * 
     * @param PeriodInterface $period The period to book.
     * @return boolean <b>True</b> if the period was booked successfully, <b>false</b> otherwise.
     */
    public function book(PeriodInterface $period)
    {
        return $this->getAvailability()->addBooking($period);
    }
    
}
