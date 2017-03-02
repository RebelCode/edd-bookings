<?php

namespace RebelCode\Block\Html;

/**
 * Description of LinkTag
 *
 * @since [*next-version*]
 */
class LinkTag extends RegularTag
{
    const TAG_NAME = 'a';

    const TARGET_NEW_TAB = '_blank';
    const TARGET_SELF = '_self';

    public function __construct($text, $url, $newTab = false, array $attributes = array())
    {
        $attrs = array(
            'href'    => is_null($url)
                ? 'javascript:void(0)'
                : $url,
            '_target' => $newTab
                ? static::TARGET_NEW_TAB
                : static::TARGET_SELF
        );
        $allAttributes = array_merge($attrs, $attributes);

        parent::__construct(static::TAG_NAME, $allAttributes, $text);
    }
}
