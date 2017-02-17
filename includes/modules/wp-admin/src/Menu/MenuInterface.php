<?php

namespace RebelCode\WordPress\Admin\Menu;

use \RebelCode\WordPress\Admin\Page;

/**
 * Anything that can represent a WordPress admin menu.
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
     * Gets the menu page callback.
     *
     * @since [*next-version*]
     *
     * @return callable The page callback.
     */
    public function getPage();

    /**
     * Registers the menu with WordPress.
     *
     * @since [*next-version*]
     */
    public function register();

    /**
     * Gets the menu position.
     *
     * @since [*next-version*]
     *
     * @return int The menu position.
     */
    public function getPosition();
}
