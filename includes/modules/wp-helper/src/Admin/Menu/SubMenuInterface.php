<?php

namespace RebelCode\Wp\Admin\Menu;

/**
 * Represents a WordPress admin menu that is nested under a top-level menu.
 *
 * @since [*next-version*]
 */
interface SubMenuInterface
{
    /**
     * Gets the parent top-level menu.
     *
     * @since [*next-version*]
     *
     * @return MainMenuInterface
     */
    public function getParentMenu();
}
