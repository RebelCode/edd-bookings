<?php

namespace Aventura\Diary\DateTime;

use \Aventura\Diary\DateTime\Duration\DurationAbstract;

/**
 * Duration
 *
 * @author Miguel Muscat <miguelmuscat93@gmail.com>
 */
class Duration extends DurationAbstract
{

    /**
     * Number of seconds in a single second.
     *
     * @var integer
     */
    const SECONDS_IN_SECOND = 1;

    /**
     * Number of seconds in a single minute.
     *
     * @var integer
     */
    const SECONDS_IN_MINUTE = 60;

    /**
     * Number of seconds in a single hour.
     *
     * @var integer
     */
    const SECONDS_IN_HOUR = 3600;

    /**
     * Number of seconds in a single day.
     *
     * @var integer
     */
    const SECONDS_IN_DAY = 86400;

    /**
     * Number of seconds in a single week.
     *
     * @var integer
     */
    const SECONDS_IN_WEEK = 604800;

    /**
     * Assoc array of unit names and their respective number of seconds.
     * 
     * @var array
     */
    protected static $unitsInSeconds = array(
        'second' => self::SECONDS_IN_SECOND,
        'minute' => self::SECONDS_IN_MINUTE,
        'hour' => self::SECONDS_IN_HOUR,
        'day' => self::SECONDS_IN_DAY,
        'week' => self::SECONDS_IN_WEEK
    );

    /**
     * The duration length in seconds.
     * 
     * @var integer
     */
    protected $_seconds;

    /**
     * Constructs a new instance.
     * 
     * @param integer $seconds The number of seconds.
     */
    public function __construct($seconds)
    {
        $this->setSeconds($seconds);
    }
    
    /**
     * Gets the duration length, in seconds.
     * 
     * @return integer The length of the duration, in seconds.
     */
    public function getSeconds()
    {
        return $this->_seconds;
    }

    /**
     * Sets the length of the duration, in seconds.
     * 
     * @param integer $seconds The new length of the duration, in seconds.
     * @return Duration This instance.
     */
    public function setSeconds($seconds)
    {
        $this->_seconds = $seconds;
        return $this;
    }

    /**
     * Gets the string representation for this duration.
     * 
     * @return string
     */
    public function __toString()
    {
        return self::beautify($this->getSeconds());
    }

    /**
     * Translates the given number of seconds into a human-readable string, in the format:
     *
     * w weeks, d days, h hours, m minutes, s seconds
     *
     * If any of the variables w, d, h, m or s are zero, that section of the string is ommitted.
     *
     * @param integer $seconds The number of seconds to translate
     * @param string $separator Optional string that separates the sections in the resulting string. Default: ", "
     * @return string
     */
    public static function beautify($seconds, $separator = ', ')
    {
        $result = array();
        foreach (array_reverse(self::$unitsInSeconds) as $unit => $inSeconds) {
            $amount = floor($seconds / $inSeconds);
            if ($amount > 0) {
                $suffix = $amount > 1 ? 's' : '';
                $result[] = sprintf('%1$s %2$s%3$s', $amount, $unit, $suffix);
            }
            $seconds = floor($seconds % $inSeconds);
        }
        return implode($separator, $result);
    }

    /**
     * Returns the number of seconds in a second, or a specific number of seconds.
     * 
     * This method only exists for the sole purpose of completeness.
     * 
     * @param float $seconds Optional number of seconds. Default: 1
     * @param boolean $object If true, the method will return a DateTimeDuration instance. Otherwise, an integer is returned. Default: true
     * @return mixed If the $object param is false, the number of seconds given by the parameter. Otherwise, a DateTimeDuration instance representing a duration of the same amount of seconds.
     */
    public static function seconds($seconds = 1, $object = true)
    {
        $s = intval(self::SECONDS_IN_SECOND * $seconds);
        return $object ? new static($s) : $s;
    }

    /**
     * Returns the number of seconds in a minute, or a specific number of minutes.
     * 
     * @param float $minutes Optional number of minutes. Default: 1
     * @param boolean $object If true, the method will return a DateTimeDuration instance. Otherwise, an integer is returned. Default: true
     * @return mixed If the $object param is false, the number of seconds for the number of minutes given by the parameter. Otherwise, a DateTimeDuration instance representing a duration of the same amount of seconds.
     */
    public static function minutes($minutes = 1, $object = true)
    {
        $seconds = intval(self::SECONDS_IN_MINUTE * $minutes);
        return $object ? new static($seconds) : $seconds;
    }

    /**
     * Returns the number of seconds in a hour, or a specific number of hours.
     * 
     * @param float $hours Optional number of hours. Default: 1
     * @param boolean $object If true, the method will return a DateTimeDuration instance. Otherwise, an integer is returned. Default: true
     * @return mixed If the $object param is false, the number of seconds for the number of hours given by the parameter. Otherwise, a DateTimeDuration instance representing a duration of the same amount of seconds.
     */
    public static function hours($hours = 1, $object = true)
    {
        $seconds = intval(self::SECONDS_IN_HOUR * $hours);
        return $object ? new static($seconds) : $seconds;
    }

    /**
     * Returns the number of seconds in a day, or a specific number of days.
     * 
     * @param float $days Optional number of days. Default: 1
     * @param boolean $object If true, the method will return a DateTimeDuration instance. Otherwise, an integer is returned. Default: true
     * @return mixed If the $object param is false, the number of seconds for the number of days given by the parameter. Otherwise, a DateTimeDuration instance representing a duration of the same amount of seconds.
     */
    public static function days($days = 1, $object = true)
    {
        $seconds = intval(self::SECONDS_IN_DAY * $days);
        return $object ? new static($seconds) : $seconds;
    }

    /**
     * Returns the number of seconds in a week, or a specific number of weeks.
     * 
     * @param float $weeks Optional number of weeks. Default: 1
     * @param boolean $object If true, the method will return a DateTimeDuration instance. Otherwise, an integer is returned. Default: true
     * @return mixed If the $object param is false, the number of seconds for the number of weeks given by the parameter. Otherwise, a DateTimeDuration instance representing a duration of the same amount of seconds.
     */
    public static function weeks($weeks = 1, $object = true)
    {
        $seconds = intval(self::SECONDS_IN_WEEK * $weeks);
        return $object ? new static($seconds) : $seconds;
    }

}
