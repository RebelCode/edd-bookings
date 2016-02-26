<?php

namespace Aventura\Diary\DateTime\Duration;

use \Aventura\Diary\DateTime\Arithmetical\ArithmeticalInterface;

/**
 * Represents a specific length of time, without any particular starting point.
 * 
 * @author Miguel Muscat <miguelmuscat93@gmail.com>
 */
interface DurationInterface extends ArithmeticalInterface
{

    /**
     * Gets the number of seconds in this duration.
     * 
     * @return integer The duration, as seconds.
     */
    public function getSeconds();

    /**
     * Checks if this instance is equal to the given parameter instance.
     * 
     * @param DurationInterface $other The other instance to compare to.
     * @return boolean True if this instance and the parameter instance are equal, false if not.
     */
    public function isEqualTo(DurationInterface $other);

}
