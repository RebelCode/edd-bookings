<?php

namespace RebelCode\Block\Html;

/**
 * Abstract functionality of an HTML tag.
 *
 * @since [*next-version*]
 */
abstract class AbstractTag extends AbstractHtmlBlock
{
    /**
     * The default tag name.
     *
     * @since [*next-version*]
     */
    const TAG_NAME = 'div';

    /**
     * The tag name.
     *
     * @since [*next-version*]
     *
     * @var string
     */
    protected $tagName;

    /**
     * The content.
     *
     * @since [*next-version*]
     *
     * @var string
     */
    protected $content;

    /**
     * The attributes.
     *
     * @since [*next-version*]
     *
     * @var array
     */
    protected $attributes;

    /**
     * Gets the tag name.
     *
     * Defaults to the value of the TAG_NAME class constant (late statically bound) if the tag name is null.
     *
     * @see TAG_NAME
     *
     * @since [*next-version*]
     *
     * @return string
     */
    protected function _getTagName()
    {
        return is_null($this->tagName)
            ? static::TAG_NAME
            : $this->tagName;
    }

    /**
     * Sets the tag name.
     *
     * @since [*next-version*]
     *
     * @param string $tagName The new tag name.
     *
     * @return $this This instance.
     */
    protected function _setTagName($tagName)
    {
        $this->tagName = $tagName;

        return $this;
    }

    /**
     * Gets the tag content.
     *
     * @since [*next-version*]
     *
     * @return string The tag content.
     */
    protected function _getContent()
    {
        return $this->content;
    }

    /**
     * Sets the tag content.
     *
     * @since [*next-version*]
     *
     * @param string $content The new tag content.
     *
     * @return $this This instance.
     */
    protected function _setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Gets the tag attributes or a subset of the tag attributes.
     *
     * @since [*next-version*]
     *
     * @param array|null $attributes An optional array to white-list the attributes to return.
     *                               Default: null
     *
     * @return array An associative array of attribute values mapped using attribute names as keys.
     *               If the $attributes argument is null, all of this tag's attributes will be returned.
     *               Otherwise, only the tags with names included in the $attributes array (as values)
     *               are returned.
     */
    protected function _getAttributes($attributes = null)
    {
        return is_null($attributes)
            ? $this->attributes
            : array_intersect_key($this->attributes, $attributes);
    }

    /**
     * Sets all of the tag's attributes.
     *
     * @since [*next-version*]
     *
     * @param array $attributes An array containing the attribute values mapped using attribute names
     *                          as keys.
     *
     * @return $this This instance.
     */
    protected function _setAttributes(array $attributes)
    {
        $this->attributes = $attributes;

        return $this;
    }

    /**
     * Sets an attribute for this tag.
     *
     * @since [*next-version*]
     *
     * @param string $name  The attribute name.
     * @param mixed  $value The attribute value.
     *
     * @return $this This instance.
     */
    protected function _setAttribute($name, $value)
    {
        $this->attributes[$name] = $value;

        return $this;
    }

    /**
     * Gets the value of an attribute.
     *
     * @since [*next-version*]
     *
     * @param string $name    The name of the tag whose value is to be returned.
     * @param mixed  $default The default value to return if the attribute does not exist for this tag.
     *                        Default: null
     *
     * @return mixed The value of the attribute with the given name, if this tag has an attribute with that
     *               name; otherwise, the value of the $default argument.
     */
    protected function _getAttribute($name, $default = null)
    {
        return $this->_hasAttribute($name)
            ? $this->attributes[$name]
            : $default;
    }

    /**
     * Checks if this tag has an attribute, by name.
     *
     * @since [*next-version*]
     *
     * @param string $name The name of the attribute to search for.
     *
     * @return bool True if the tag has an attribute with the given name, false otherwise.
     */
    protected function _hasAttribute($name)
    {
        return isset($this->attributes[$name]);
    }

    /**
     * Removes an attribute from this tag.
     *
     * @since [*next-version*]
     *
     * @param string $name The name of the attribute to remove.
     *
     * @return $this This instance.
     */
    protected function _removeAttribute($name)
    {
        unset($this->attributes[$name]);

        return $this;
    }
}
