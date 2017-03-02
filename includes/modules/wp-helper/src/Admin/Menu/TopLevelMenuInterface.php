<?php

namespace RebelCode\Wp\Admin\Menu;

/**
 * Represents a top-level WordPress admin menu.
 *
 * @since [*next-version*]
 */
interface TopLevelMenuInterface extends MenuInterface
{
    /**
     * Gets the icon to show for this menu.
     *
     * @since [*next-version*]
     *
     * @return string
     */
    public function getIcon();

    /**
     * Gets the menu position.
     *
     * @since [*next-version*]
     *
     * @return int The menu position.
     */
    public function getPosition();
}
