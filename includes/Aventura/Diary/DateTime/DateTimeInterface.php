<?php

namespace Aventura\Diary\DateTime;

use \Aventura\Diary\DateTime\Arithmetical\ArithmeticalInterface;
use \Aventura\Diary\DateTime\Comparable\ComparableInterface;

/**
 * Describes an entity that represents a particular point in time (timestamp).
 * 
 * @author Miguel Muscat <miguelmuscat93@gmail.com>
 */
interface DateTimeInterface extends ComparableInterface, ArithmeticalInterface
{

    /**
     * Gets the UTC timestamp.
     * 
     * @return integer The UTC timestamp for this date.
     */
    public function getTimestamp();
    
    /**
     * Creates a copy of this instance.
     * 
     * @return DateTimeInterface A copy of this instance.
     */
    public function copy();
    
    /**
     * Gets this instance's timestamp for the time only.
     * 
     * @return DateTimeInterface A new instance with this instance's time.
     */
    public function getTime();
    
    /**
     * Gets this instance's timestamp for the date only.
     * 
     * @return DateTimeInterface A new instance with this instance's date.
     */
    public function getDate();
    
    /**
     * Formats the date and/or time.
     * 
     * @param string $format The PHP date() format.
     * @return string
     */
    public function format($format);

}
