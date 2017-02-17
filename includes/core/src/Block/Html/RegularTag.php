<?php

namespace RebelCode\EddBookings\Block\Html;

/**
 * An implementation of a regular HTML tag.
 *
 * A regular tag is one that requires a closing tag, i.e. is not a self-closing tag.
 *
 * @since [*next-version*]
 */
class RegularTag extends AbstractGenericTag implements TagInterface
{
    /**
     * Constructor.
     *
     * @since [*next-version*]
     *
     * @param string $tag        The tag name.
     * @param array  $attributes The tag attributes. Default: array()
     * @param string $content    The tag content. Default: string
     */
    public function __construct($tag, $attributes = array(), $content = '')
    {
        $this->_setTagName($tag)
            ->_setAttributes($attributes)
            ->_setContent($content);
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
        $content = $this->getContent();
        $attrs   = $this->getAttributes();

        // Prepare attributes string with leading spaces
        $attrsString = count($attrs)
            ? ' ' . $this->_getAttrsString($attrs)
            : '';
        return sprintf('<%1$s%2$s>%3$s</%1$s>', $tagName, $attrsString, $content);
    }
}
