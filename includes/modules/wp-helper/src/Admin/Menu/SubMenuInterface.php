<?php

namespace RebelCode\Wp\Admin\Menu;

/**
 * Represents a WordPress admin menu that is nested under a top-level menu.
 *
 * @since [*next-version*]
 */
interface SubMenuInterface extends MenuInterface
{
    /**
     * Gets the parent top-level menu.
     *
     * @since [*next-version*]
     *
     * @return TopLevelMenuInterface|string The parent menu instance or ID.
     */
    public function getParentMenu();
}
