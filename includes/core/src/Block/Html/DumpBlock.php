<?php

namespace RebelCode\EddBookings\Block\Html;

/**
 * Description of DumpTag
 *
 * @since [*next-version*]
 */
class DumpBlock extends RegularTag
{
    const TAG_NAME = 'pre';

    public function __construct($var)
    {
        parent::__construct(static::TAG_NAME, array(), print_r($var, true));
    }
}
