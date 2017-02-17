<?php

namespace RebelCode\EddBookings\Block\Html;

/**
 * Description of HeadingTag
 *
 * @since [*next-version*]
 */
class HeadingTag extends RegularTag
{

    const TAG_NAME = 'h';

    public function __construct($level, $content = '', $attributes = array())
    {
        $tag = sprintf('%s%d', static::TAG_NAME, $level);

        parent::__construct($tag, $attributes, $content);
    }
}
