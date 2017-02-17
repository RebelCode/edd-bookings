<?php

namespace RebelCode\EddBookings\Block\Html;

/**
 * Something that represents an HTML tag.
 *
 * @since [*next-version*]
 */
interface TagInterface
{
    /**
     * Gets the attributes for this tag.
     *
     * @param array|null $attributes An array of attribute names to return or null to return all attributes.
     *
     * @return array An associative array of attribute values mapped using attribute names as keys.
     */
    public function getAttributes($attributes = null);

    /**
     * Gets the content of the tag.
     *
     * @since [*next-version*]
     *
     * @return string The text content of the tag.
     */
    public function getContent();

    /**
     * Gets the name of the tag.
     *
     * @since [*next-version*]
     *
     * @return string The tag name.
     */
    public function getTagName();
}
