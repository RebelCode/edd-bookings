<?php

namespace RebelCode\Wp\Admin\Menu;

use RebelCode\Wp\Admin\Page\PageInterface;

/**
 * Represents a WordPress menu that renders a page.
 *
 * @see PageInterface
 *
 * @since [*next-version*]
 */
interface PageMenuInterface extends MenuInterface
{
    /**
     * Gets the page that the menu links to.
     *
     * @since [*next-version*]
     *
     * @return PageInterface
     */
    public function getPage();
}
