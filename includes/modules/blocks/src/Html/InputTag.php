<?php

namespace RebelCode\Block\Html;

/**
 * Description of InputTag
 *
 * @since [*next-version*]
 */
class InputTag extends SelfClosingTag
{
    const TAG_NAME = 'input';

    public function __construct($type, $id, $name, $value, $attributes = array())
    {
        $finalAttrs = array_merge(array(
            'type'  => $type,
            'id'    => $id,
            'name'  => $name,
            'value' => $value
        ), $attributes);

        parent::__construct(static::TAG_NAME, '', $finalAttrs);
    }

}
