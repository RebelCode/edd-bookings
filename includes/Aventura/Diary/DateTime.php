<?php

namespace Aventura\Diary;

use \Aventura\Diary\DateTime\DateTimeAbstract;
use \InvalidArgumentException;

/**
 * DateTime
 * 
 * @author Miguel Muscat <miguelmuscat93@gmail.com>
 */
class DateTime extends DateTimeAbstract
{
    
    const TO_STRING_FORMAT = 'H:i:s - d/M/Y';
    
    /**
     * The UTC timestamp, in seconds.
     * 
     * @var integer
     */
    protected $_timestamp;

    /**
     * Constructs a new instance.
     * 
     * @param mixed $arg A DateTimeAbstract instance for cloning or a numeric value as a UTC timestamp.
     * @throws InvalidArgumentException If the argument is not a numeric value or a DateTimeAbstract instance.
     */
    public function __construct($arg)
    {
        if (is_int($arg) || is_numeric($arg)) {
            $timestamp = intval($arg);
        } else if (is_a($arg, __CLASS__)) {
            $timestamp = $arg->getTimestamp();
        } else {
            throw new InvalidArgumentException(sprintf('Argument #1 must be a numeric value or a %s instance.', __CLASS__));
        }
        $this->setTimestamp($timestamp);
    }

    /**
     * {@inheritdoc}
     * 
     * @return integer The UTC timestamp for this date/time.
     */
    public function getTimestamp()
    {
        return $this->_timestamp;
    }

    /**
     * Sets the timestamp.
     * 
     * @param integer $timestamp The new timestamp, as (UTC) seconds.
     * @return DateTimeAbstract This instance.
     */
    public function setTimestamp($timestamp)
    {
        $this->_timestamp = $timestamp;
        return $this;
    }
    
    /**
     * Gets this instance's timestamp for the time only.
     * 
     * @return DateTime A new instance with this instance's time.
     */
    public function getTime()
    {
        return DateTime::fromString($this->format('H:i:s'), 0);
    }
    
    /**
     * Gets this instance's timestamp for the date only.
     * 
     * @return DateTime A new instance with this instance's date.
     */
    public function getDate()
    {
        return new DateTime($this->getTimestamp() - $this->getTime()->getTimestamp());
    }
    
    /**
     * {@inheritdoc}
     * 
     * @return DateTime A copy of this instance.
     */
    public function copy()
    {
        return new static($this->getTimestamp());
    }
    
    /**
     * Formats the date and/or time.
     * 
     * @param string $format The PHP date() format.
     * @return string
     */
    public function format($format)
    {
        return gmdate($format, $this->getTimestamp());
    }
    
    /**
     * Gets the string representation of the time for this instance.
     * 
     * @return string
     */
    public function __toString()
    {
        return $this->format(self::TO_STRING_FORMAT);
    }

    /**
     * Creates an instance for the current date and time.
     * 
     * @return DateTime The instance with the UTC timestamp for the current date and time.
     */
    public static function now()
    {
        return new static(static::nowTimestamp());
    }

    /**
     * Gets the timestamp for the current UTC date and time.
     * 
     * @return integer UTC timestamp for the current date and time.
     */
    public static function nowTimestamp()
    {
        return intval(gmdate('U'));
    }

    /**
     * Parses English text into a timestamp and creats a DateTime instance.
     * 
     * @param string $time A date/time string. Valid formats are explained in Date and Time Formats.
     * @param integer $now [optional] Default: <b>null</b><br/>The timestamp which is used as a base for the calculation of relative dates.
     * @return DateTime A Datetime instance with the calculated timestamp on success, <b>null</b> otherwise.
     */
    public static function fromString($time, $now = null)
    {
        $timestamp = strtotime($time, is_null($now) ? static::nowTimestamp() : $now);
        return ($timestamp !== false) ? new static($timestamp) : null;
    }

}
