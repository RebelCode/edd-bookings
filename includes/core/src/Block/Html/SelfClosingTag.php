<?php

namespace RebelCode\EddBookings\Block\Html;

/**
 * An implementation of a self-closing HTML tag.
 *
 * A self-closing tag is one that does not require a closing tag.
 *
 * @since [*next-version*]
 */
class SelfClosingTag extends AbstractGenericTag implements TagInterface
{
    /**
     * Constructor.
     *
     * @since [*next-version*]
     *
     * @param string $tag        The tag name.
     * @param string $content    The tag content.
     * @param array  $attributes The tag attributes. Default: array()
     */
    public function __construct($tag, $content = '', $attributes = array())
    {
        $this->_setTagName($tag)
            ->_setContent($content)
            ->_setAttributes($attributes);
    }

    /**
     * {@inheritdoc}
     *
     * @since [*next-version*]
     */
    protected function _getOutput()
    {
        // Get tag information
        $tagName = $this->getTagName();
        $attrs   = $this->getAttributes();

        // Prepare attributes string with leading spaces
        $attrsString = count($attrs)
            ? ' ' . $this->_getAttrsString($attrs)
            : '';

        return sprintf('<%1$s%2$s>', $tagName, $attrsString);
    }
}
