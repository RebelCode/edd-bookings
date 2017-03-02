<?php

namespace RebelCode\Block\Html;

/**
 * Description of FaIcon
 *
 * @since [*next-version*]
 */
class FaIcon extends RegularTag
{
    const TAG_NAME = 'i';

    public function __construct($icon, array $attributes = array())
    {
        $preClasses = isset($attributes['class'])
            ? $attributes['class']
            : '';
        $attributes['class'] = sprintf('fa fa-fw fa-%s %s', $icon, $preClasses);

        parent::__construct(static::TAG_NAME, $attributes, '');
    }
}
