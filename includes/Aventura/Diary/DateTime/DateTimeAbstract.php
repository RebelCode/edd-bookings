<?php

namespace Aventura\Diary\DateTime;

use \Aventura\Diary\DateTime\Comparable\ComparableAbstract;

/**
 * DateTimeAbstract
 *
 * @author Miguel Muscat <miguelmuscat93@gmail.com>
 */
abstract class DateTimeAbstract extends ComparableAbstract implements DateTimeInterface
{

    /**
     * {@inheritdoc}
     * 
     * @return integer The UTC timestamp for this date/time.
     */
    abstract public function getTimestamp();

    /**
     * Sets the timestamp.
     * 
     * @param integer $timestamp The new timestamp, as (UTC) seconds.
     * @return DateTimeAbstract This instance.
     */
    abstract public function setTimestamp($timestamp);

    /**
     * {@inheritdoc}
     */
    public function getArithmeticValue()
    {
        return $this->getTimestamp();
    }
    
    /**
     * {@inheritdoc}
     * 
     * @param integer $value The new value.
     * @return DateTimeAbstract This instance.
     */
    public function setArithmeticValue($value)
    {
        return $this->setTimestamp($value);
    }

    /**
     * Gets the string representation of this instance.
     * 
     * @return string The timestamp as a string.
     */
    public function __toString()
    {
        return strval($this->getTimestamp());
    }
    
}
