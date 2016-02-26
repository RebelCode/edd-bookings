<?php

namespace Aventura\Diary\DateTime;

use \Aventura\Diary\DateTime\Duration\DurationInterface;
use \Aventura\Diary\DateTime\Period\PeriodAbstract;

/**
 * Description of Period
 *
 * @author Miguel Muscat <miguelmuscat93@gmail.com>
 */
class Period extends PeriodAbstract
{

    /**
     * The starting date/time of the period.
     * 
     * @var DateTimeInterface
     */
    protected $_start;

    /**
     * The duration of the period.
     * 
     * @var DurationInterface
     */
    protected $_duration;

    /**
     * Constructs a new instance.
     * 
     * @param DateTimeInterface $start The period's starting date/time
     * @param DurationInterface $duration The period's duration
     */
    public function __construct($start, $duration)
    {
        $this->setStart($start)
                ->setDuration($duration);
    }

    /**
     * Gets the period's starting date/time.
     * 
     * @return DateTimeInterface
     */
    public function getStart()
    {
        return $this->_start;
    }

    /**
     * Gets the period's duration.
     * 
     * @return DurationInterface
     */
    public function getDuration()
    {
        return $this->_duration;
    }

    /**
     * Calculates and gets the period's ending date/time/
     * 
     * @return DateTimeInterface
     */
    public function getEnd()
    {
        return $this->getStart()->copy()->plus($this->getDuration())->minus(Duration::seconds(1));
    }

    /**
     * Sets the period's starting date/time.
     * 
     * @param DateTimeInterface $start The new starting date/time.
     * @return Period This instance.
     */
    public function setStart($start)
    {
        $this->_start = $start;
        return $this;
    }

    /**
     * Sets the period's duration.
     * 
     * @param DurationInterface $duration The new duration.
     * @return Period This instance.
     */
    public function setDuration($duration)
    {
        $this->_duration = $duration;
        return $this;
    }

    /**
     * Formats the DateTimePeriod into a string.
     * 
     * @param string $format The format string.
     * 
     *      This method will search the given format string for placeholders and replace them
     *      with data from this DateTimePeriod instance. The available placeholders are:
     * 
     *      "%s" - The starting time. See DateTime::format() <br/>
     *      "%d" - The duration. See DateTimeDuration::format() <br/>
     *      "%e" - The ending time. See DateTime::format() <br/>
     * 
     * @return string A string containing the formatted data for this instance, according to the format string given.
     */
    public function format($format)
    {
        return str_replace(
            array(
                '%s',
                '%e',
                '%d'
            ), array(
                $this->getStart()->__toString(),
                $this->getEnd()->__toString(),
                $this->getDuration()->__toString()
            ), $format
        );
    }

    /**
     * Gets the string representation of this instance, in the following format:
     *
     * "<start>, <duration>"
     *
     * @uses Period::format()
     * @return string
     */
    public function __toString()
    {
        return $this->format('%s, %d');
    }

}
