<?php

namespace Aventura\Diary\DateTime\Arithmetical;

/**
 * Represents a date/time entity that can be used in time arithmetic operations.
 * 
 * @author Miguel Muscat <miguelmuscat93@gmail.com>
 */
interface ArithmeticalInterface
{

    /**
     * Gets a value, in seconds, that can be used in arithmetic time operations.
     * 
     * @return integer The time value, in seconds
     */
    public function getArithmeticValue();

    /**
     * Adds another instance's value to this instance's.
     * 
     * @param ArithmeticalInterface $other The other operand who's value is to be added.
     */
    public function plus(ArithmeticalInterface $other);

    /**
     * Subtracts another instance's value to this instance's.
     * 
     * @param ArithmeticalInterface $other The other operand who's value is to be subtracted.
     */
    public function minus(ArithmeticalInterface $other);

    /**
     * Multiples this instance's value by another instance's.
     * 
     * @param ArithmeticalInterface $other The other operand who's value is to be multiplied.
     */
    public function mult(ArithmeticalInterface $other);

    /**
     * Divides this instance's value by another instance's.
     * 
     * @param ArithmeticalInterface $other The other operand who's value is to be divided.
     */
    public function divide(ArithmeticalInterface $other);

}
