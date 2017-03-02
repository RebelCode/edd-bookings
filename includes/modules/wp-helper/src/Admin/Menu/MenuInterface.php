<?php

namespace RebelCode\Wp\Admin\Menu;

/**
 * Represents a WordPress admin menu.
 *
 * @since [*next-version*]
 */
interface MenuInterface
{
    /**
     * Gets the menu ID.
     *
     * @since [*next-version*]
     *
     * @return int
     */
    public function getId();

    /**
     * Gets the menu label.
     *
     * @since [*next-version*]
     *
     * @return string The menu label.
     */
    public function getLabel();

    /**
     * Gets the content to be displayed when this menu is selected.
     *
     * @since [*next-version*]
     *
     * @return callable|BlockInterface|string|null A callback function, block, URL or null.
     */
    public function getContent();

    /**
     * Gets the required user capability for this menu to be displayed.
     *
     * @since [*next-version*]
     *
     * @return string
     */
    public function getCapability();

    /**
     * Registers the menu with WordPress.
     *
     * @since [*next-version*]
     */
    public function register();
}
