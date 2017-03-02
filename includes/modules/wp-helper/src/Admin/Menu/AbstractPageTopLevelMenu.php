<?php

namespace RebelCode\Wp\Admin\Menu;

/**
 * Basic functionality for a top-level menu that renders a page.
 *
 * @since [*next-version*]
 */
abstract class AbstractPageTopLevelMenu extends AbstractTopLevelMenu
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
