<?php

namespace RebelCode\EddBookings\Block\Html;

/**
 * Description of FaSpinningIcon
 *
 * @since [*next-version*]
 */
class FaSpinningIcon extends FaIcon
{
    public function __construct($icon, array $attributes = array())
    {
        parent::__construct(sprintf('%s fa-spin', $icon), $attributes);
    }
}
