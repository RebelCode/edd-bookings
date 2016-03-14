<?php

namespace Aventura\Diary\DateTime\Duration;

use \Aventura\Diary\DateTime\Arithmetical\ArithmeticalAbstract;

/**
 * Description of DurationAbstract
 *
 * @author Miguel Muscat <miguelmuscat93@gmail.com>
 */
abstract class DurationAbstract extends ArithmeticalAbstract implements DurationInterface
{
    
    /**
     * Gets the lrngth of this duration in seconds.
     * 
     * @return integer The length of this duration in seconds.
     */
    abstract public function getSeconds();
    
    /**
     * Sets the length of this duration in seconds.
     * 
     * @param integer $seconds The new length, in seconds.
     * @return DurationAbstract This instance.
     */
    abstract public function setSeconds($seconds);

    /**
     * {@inheritdoc}
     * 
     * @param DurationInterface $other The other instance to compare to.
     * @return boolean <b>True</b> if this instance and $other and equal, <b>false</b> otherwise.
     */
    public function isEqualTo(DurationInterface $other)
    {
        return $this->getSeconds() === $other->getSeconds();
    }

    /**
     * {@inheritdoc}
     * 
     * @uses DurationAbstract::getSeconds()
     * @return integer The value used for arithmetic operations.
     */
    public function getArithmeticValue()
    {
        return $this->getSeconds();
    }

    /**
     * {@inheritdoc}
     * 
     * @uses DurationAbstract::setSeconds()
     * @param integer $value The new value
     * @return DurationAbstract This insance.
     */
    public function setArithmeticValue($value)
    {
        return $this->setSeconds($value);
    }
    
    /**
     * {@inheritdoc}
     */
    public function copy()
    {
        return new static($this->getSeconds());
    }

}
