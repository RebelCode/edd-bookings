<?php

namespace RebelCode\Wp\Admin\Menu;

use RebelCode\EddBookings\Block\BlockInterface;
use RebelCode\Wp\Admin\Page\PageInterface;

/**
 * Basic functionality of a menu.
 *
 * @since [*next-version*]
 */
abstract class AbstractMenu
{
    /**
     * The menu ID.
     *
     * @since [*next-version*]
     *
     * @var string
     */
    protected $id;

    /**
     * The menu label.
     *
     * @since [*next-version*]
     *
     * @var string
     */
    protected $label;

    /**
     * The menu position.
     *
     * @since [*next-version*]
     *
     * @var int
     */
    protected $position;

    /**
     * The icon dashicons name or URL.
     *
     * @since [*next-version*]
     *
     * @var string
     */
    protected $icon;

    /**
     * The minimum required capability for this menu to be displayed to the user.
     *
     * @since [*next-version*]
     *
     * @var string
     */
    protected $requiredCapability;

    /**
     * A callback function or block that render the content or a URL to redirect to.
     *
     * @since [*next-version*]
     *
     * @var callable|BlockInterface|string|null
     */
    protected $content;

    /**
     * Gets the menu ID.
     *
     * @since [*next-version*]
     *
     * @return string
     */
    protected function _getId()
    {
        return $this->id;
    }

    /**
     * Gets the menu label.
     *
     * @since [*next-version*]
     *
     * @return string
     */
    protected function _getLabel()
    {
        return $this->label;
    }

    /**
     * Gets the menu position.
     *
     * @since [*next-version*]
     *
     * @return int
     */
    protected function _getPosition()
    {
        return $this->position;
    }

    /**
     * Gets the icon to show for this menu.
     *
     * @since [*next-version*]
     *
     * @return string A URL or dashicons icon name.
     */
    protected function _getIcon()
    {
        return $this->icon;
    }

    /**
     * Gets the content to be displayed when this menu is selected.
     *
     * @since [*next-version*]
     *
     * @return callable|PageInterface|BlockInterface|string|null A callback function, page, block, URL or null.
     */
    protected function _getContent()
    {
        return $this->content;
    }

    /**
     * Gets the minimum required user capability for this menu to be displayed.
     *
     * @since [*next-version*]
     *
     * @return string
     */
    protected function _getRequiredCapability()
    {
        return $this->requiredCapability;
    }

    /**
     * Sets the menu ID.
     *
     * @since [*next-version*]
     *
     * @param string $id
     *
     * @return $this
     */
    protected function _setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Sets the menu label.
     *
     * @since [*next-version*]
     *
     * @param string $label
     *
     * @return $this
     */
    protected function _setLabel($label)
    {
        $this->label = $label;

        return $this;
    }

    /**
     * Sets the menu position.
     *
     * @since [*next-version*]
     *
     * @param int $position
     *
     * @return $this
     */
    protected function _setPosition($position)
    {
        $this->position = $position;

        return $this;
    }

    /**
     * Sets the icon to show for this menu.
     *
     * @since [*next-version*]
     *
     * @param string $icon A URL or dashicons icon name.
     */
    protected function _setIcon($icon)
    {
        $this->icon = $icon;

        return $this;
    }

    /**
     * Sets the content to be displayed when this menu is selected.
     *
     * @since [*next-version*]
     *
     * @param callable|PageInterface|BlockInterface|string|null $content A callback function, page, block, URL or null.
     *
     * @return $this
     */
    protected function _setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Sets the required user capability for this menu to be displayed.
     *
     * @since [*next-version*]
     *
     * @param string $requiredCapability The required user capability.
     *
     * @return $this
     */
    protected function _setRequiredCapability($requiredCapability)
    {
        $this->requiredCapability = $requiredCapability;

        return $this;
    }

    /**
     * Gets the title of the page rendered by this menu.
     *
     * @since [*next-version*]
     *
     * @return string
     */
    protected function _getPageTitle()
    {
        $content = $this->_getContent();

        return $content instanceof PageInterface
            ? $content->getTitle()
            : $this->_getLabel();
    }

    /**
     * Normalizes the content.
     *
     * Checks if the content is a callback or can be wrapped in a callback.
     * If the content is a URL, it is simply returned.
     *
     * An empty string is returned otherwise. WordPress detects the empty string and
     * renders nothing this menu.
     *
     * @since [*next-version*]
     *
     * @param mixed $content The content to normalize.
     *
     * @return callable|string The callback render function, a URL string or an empty string.
     */
    protected function _normalizeContent($content)
    {
        if (is_callable($content)) {
            return $content;
        }

        if (filter_var($content, FILTER_VALIDATE_URL)) {
            return $content;
        }

        if ($content instanceof BlockInterface || is_string($content)) {
            return $this->_createRenderCallback($content);
        }

        return '';
    }

    /**
     * Creates a render callback function for the given string-like content.
     *
     * @since [*next-version*]
     *
     * @param BlockInterface|string $content The content to output from the callback.
     *
     * @return callable
     */
    protected function _createRenderCallback($content)
    {
        return function() use ($content) {
            echo $content;
        };
    }

    /**
     * Registers the menu with WordPress.
     *
     * @since [*next-version*]
     *
     * @return string The name of the event triggered when the menu is selected.
     */
    abstract protected function _register();
}
