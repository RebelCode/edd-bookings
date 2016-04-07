<?php

namespace Aventura\Diary\DateTime\Arithmetical;

/**
 * ArithmeticalAbstract
 *
 * @author Miguel Muscat <miguelmuscat93@gmail.com>
 */
abstract class ArithmeticalAbstract implements ArithmeticalInterface
{

    /**
     * Gets a value, in seconds, that can be used in arithmetic time operations.
     * 
     * @return integer The time value, in seconds
     */
    abstract public function getArithmeticValue();

    /**
     * Sets the value of this instance. This is used when calling arithmetic methods.
     * 
     * @param integer $value The new value to set to this instance.
     * @return ArithmeticalAbstract This instance.
     */
    abstract public function setArithmeticValue($value);

    /**
     * Adds another instance's value to this instance's.
     * 
     * @param ArithmeticalInterface $other The other operand who's value is to be added.
     * @return ArithmeticalAbstract This instance.
     */
    public function plus(ArithmeticalInterface $other)
    {
        return $this->setArithmeticValue($this->getArithmeticValue() + $other->getArithmeticValue());
    }

    /**
     * Subtracts another instance's value to this instance's.
     * 
     * @param ArithmeticalInterface $other The other operand who's value is to be subtracted.
     * @return ArithmeticalAbstract This instance.
     */
    public function minus(ArithmeticalInterface $other)
    {
        return $this->setArithmeticValue($this->getArithmeticValue() - $other->getArithmeticValue());
    }

    /**
     * Multiples this instance's value by another instance's.
     * 
     * @param ArithmeticalInterface $other The other operand who's value is to be multiplied.
     * @return ArithmeticalAbstract This instance.
     */
    public function mult(ArithmeticalInterface $other)
    {
        return $this->setArithmeticValue($this->getArithmeticValue() * $other->getArithmeticValue());
    }

    /**
     * Divides this instance's value by another instance's.
     * 
     * @param ArithmeticalInterface $other The other operand who's value is to be divided.
     * @return ArithmeticalAbstract This instance.
     */
    public function divide(ArithmeticalInterface $other)
    {
        return $this->setArithmeticValue(intval(floor($this->getArithmeticValue() / $other->getArithmeticValue())));
    }

}
