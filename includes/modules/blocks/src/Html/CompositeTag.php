<?php

namespace RebelCode\Block\Html;

/**
 * An HMTL tag that can be composed of multiple child tags.
 *
 * @since [*next-version*]
 */
class CompositeTag extends RegularTag
{
    /**
     * {@inheritdoc}
     *
     * @since [*next-version*]
     */
    public function __construct($tag, array $attributes = array(), array $children = array())
    {
        parent::__construct($tag, $attributes, $children);
    }

    /**
     * Adds a child tag.
     *
     * @since [*next-version*]
     *
     * @param TagInterface $child The child tag to add.
     *
     * @return $this This instance.
     */
    public function addChild(TagInterface $child)
    {
        $this->content[] = $child;

        return $this;
    }

    /**
     * Gets the child tags.
     *
     * @since [*next-version*]
     *
     * @return TagInterface[] An array of tag instances.
     */
    public function getChildren()
    {
        return $this->_getContent();
    }

    /**
     * Sets the child tags.
     *
     * @since [*next-version*]
     *
     * @param TagInterface[] $children An array of tag instances.
     *
     * @return $this This instance.
     */
    public function setChildren(array $children = array())
    {
        $this->_setContent($children);

        return $this;
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
        $content = implode('', $this->getChildren());
        $attrs   = $this->getAttributes();

        // Prepare attributes string with leading spaces
        $attrsString = count($attrs)
            ? ' ' . $this->_getAttrsString($attrs)
            : '';

        return sprintf('<%1$s%2$s>%3$s</%1$s>', $tagName, $attrsString, $content);
    }
}
