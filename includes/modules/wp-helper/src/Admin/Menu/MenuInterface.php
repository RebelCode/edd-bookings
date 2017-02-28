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
     * Gets the menu position.
     *
     * @since [*next-version*]
     *
     * @return int The menu position.
     */
    public function getPosition();

    /**
     * Registers the menu with WordPress.
     *
     * @since [*next-version*]
     */
    public function register();
}
