<?php

use \Aventura\Diary\DateTime\Comparable\ComparableInterface;

namespace Aventura\Diary\DateTime\Comparable;

/**
 * Represents a date/time entity that can be comparted with other similar entities.
 * 
 * @author Miguel Muscat <miguelmuscat93@gmail.com>
 */
interface ComparableInterface
{
    
    /**
     * Compares this instance to another instance.
     * 
     * @param ComparableInterface $other The instance to compare to.
     * @return integer Returns 1 if this instance represents a time after the given param, -1 if vice-versa and 0 if they are equal.
     */
    public function compareTo(ComparableInterface $other);

    /**
     * Checks if this instance is before the given parameter instance.
     * 
     * @param ComparableInterface $other The other instance to compare to.
     * @param boolean $orEqualTo If true, this method will return true the instances are equal.
     * @return boolean True if this instance is before the parameter instance, false if not.
     *                 If $orEqualTo is true, true is returned if the two instances are equal.
     */
    public function isBefore(ComparableInterface $other, $orEqualTo = false);

    /**
     * Checks if this instance is after the given parameter instance.
     * 
     * @param ComparableInterface $other The other instance to compare to.
     * @param boolean $orEqualTo If true, this method will return true the instances are equal.
     * @return boolean True if this instance is after the parameter instance, false if not.
     *                 If $orEqualTo is true, true is returned if the two instances are equal.
     */
    public function isAfter(ComparableInterface $other, $orEqualTo = false);

    /**
     * Checks if this instance is equal to the given parameter instance.
     * 
     * @param ComparableInterface $other The other instance to compare to.
     * @return boolean True if this instance and the parameter instance are equal, false if not.
     */
    public function isEqualTo(ComparableInterface $other);

}
