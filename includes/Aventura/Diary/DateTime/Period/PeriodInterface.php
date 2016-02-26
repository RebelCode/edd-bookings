<?php

namespace Aventura\Diary\DateTime\Period;

use \Aventura\Diary\DateTime\DateTimeInterface;
use \Aventura\Diary\DateTime\Duration\DurationInterface;

/**
 * Represents an entity 
 * 
 * @author Miguel Muscat <miguelmuscat93@gmail.com>
 */
interface PeriodInterface
{

    /**
     * Gets the starting time of the period.
     * 
     * @return DateTimeInterface
     */
    public function getStart();

    /**
     * Gets the ending time of the period.
     * 
     * @return DateTimeInterface
     */
    public function getEnd();

    /**
     * Gets the duration of the period.
     * 
     * @return DurationInterface
     */
    public function getDuration();

    /**
     * Checks if this and another instance represent the same date/time period.
     * 
     * @param \Aventura\Diary\DateTime\Period\PeriodInterface $other The other instance to compare with.
     * @return boolean <b>True</b> if the instances have equal start and duration, <b>false</b> otherwise.
     */
    public function isEqualTo(PeriodInterface $other);
    
    /**
     * Checks if this instance overlaps with another.
     * 
     * @param PeriodInterface $other The other period to compare with.
     * @return boolean <b>True</b> if the periods overlap, <b>false</b> otherwise.
     */
    public function overlaps(PeriodInterface $other);

}
