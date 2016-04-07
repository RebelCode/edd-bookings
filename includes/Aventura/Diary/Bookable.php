<?php

namespace Aventura\Diary;

use \Aventura\Diary\Bookable\Availability;
use \Aventura\Diary\Bookable\Availability\AvailabilityInterface;
use \Aventura\Diary\Bookable\BookableAbstract;

/**
 * Represents an entity that can have bookable sessions.
 *
 * @author Miguel Muscat <miguelmuscat93@gmail.com>
 */
class Bookable extends BookableAbstract
{

    /**
     * The availability for this bookable entity instance.
     * 
     * @var AvailabilityInterface 
     */
    protected $_availability;
    
    /**
     * Constructs a new instance.
     */
    public function __construct()
    {
        $this->setAvailability(new Availability);
    }
    
    /**
     * Gets the avaialbility.
     * 
     * @return AvailabilityInterface The availability.
     */
    public function getAvailability()
    {
        return $this->_availability;
    }
    
    /**
     * Sets the avilability.
     * 
     * @param AvailabilityInterface $availability The availability.
     * @return Bookable This instance.
     */
    public function setAvailability(AvailabilityInterface $availability)
    {
        $this->_availability = $availability;
        return $this;
    }



}
