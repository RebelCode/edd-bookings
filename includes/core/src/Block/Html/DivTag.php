<?php

namespace RebelCode\EddBookings\Block\Html;

/**
 * Description of DivTag
 *
 * @since [*next-version*]
 */
class DivTag extends CompositeTag
{
    /**
     * {@inheritdoc}
     *
     * @since [*next-version*]
     */
    public function __construct(array $attributes = array(), array $children = array())
    {
        parent::__construct('div', $attributes, $children);
    }
}
