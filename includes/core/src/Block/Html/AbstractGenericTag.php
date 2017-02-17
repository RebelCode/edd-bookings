<?php

namespace RebelCode\EddBookings\Block\Html;

/**
 * An implementation of an {@see AbstractTag} that exposes the functionality publicly.
 *
 * @since [*next-version*]
 */
abstract class AbstractGenericTag extends AbstractTag
{
    /**
     * Gets the tag name.
     *
     * @since [*next-version*]
     *
     * @return string
     */
    public function getTagName()
    {
        return $this->_getTagName();
    }

    /**
     * Sets the tag name.
     *
     * @since [*next-version*]
     *
     * @param string $tagName The new tag name. If null, the value of the TAG_NAME constant is used.
     *
     * @return $this This instance.
     */
    public function setTagName($tagName)
    {
        $this->_setTagName($tagName);

        return $this;
    }

    /**
     * Gets the tag content.
     *
     * @since [*next-version*]
     *
     * @return string The tag content.
     */
    public function getContent()
    {
        return $this->_getContent();
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
    public function setContent($content)
    {
        $this->_setContent($content);

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
    public function getAttributes($attributes = null)
    {
        return $this->_getAttributes($attributes);
    }

    /**
     * Sets all of the tag's attributes.
     *
     * @since [*next-version*]
     *
     * @param array $attributes An array of attribute values mapped using attribute names as keys.
     *
     * @return $this This instance.
     */
    public function setAttributes(array $attributes)
    {
        $this->_setAttributes($attributes);

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
    public function setAttribute($name, $value)
    {
        $this->_setAttribute($name, $value);

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
    public function getAttribute($name, $default = null)
    {
        return $this->_getAttribute($name, $default);
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
    public function hasAttribute($name)
    {
        return $this->_hasAttribute($name);
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
    public function removeAttribute($name)
    {
        $this->_removeAttribute($name);

        return $this;
    }
}
