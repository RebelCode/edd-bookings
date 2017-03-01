<?php

namespace RebelCode\Wp\Admin\Menu;

use RebelCode\Wp\Admin\Page\PageInterface;

/**
 * Basic functionality for a menu that renders a page.
 *
 * @since [*next-version*]
 */
abstract class AbstractPageMenu extends AbstractMenu
{
    /**
     * The page.
     *
     * @since [*next-version*]
     *
     * @var PageInterface
     */
    protected $page;

    /**
     * Gets the page displayed by this menu.
     *
     * @since [*next-version*]
     *
     * @return PageInterface
     */
    protected function _getPage()
    {
        return $this->page;
    }

    /**
     * Sets the page displayed by this menu.
     *
     * @since [*next-version*]
     *
     * @param PageInterface $page
     *
     * @return $this
     */
    protected function _setPage(PageInterface $page)
    {
        $this->page = $page;

        return $this;
    }
}
