<?php

namespace RebelCode\Wp\Admin\Menu;

/**
 * Basic functionality for a sub-menu.
 *
 * @since [*next-version*]
 */
abstract class AbstractSubMenu extends AbstractMenu
{
    /**
     * The parent top-level menu.
     *
     * @since [*next-version*]
     *
     * @var TopLevelMenuInterface
     */
    protected $parentMenu;

    /**
     * Gets the parent top-level menu.
     *
     * @since [*next-version*]
     *
     * @return TopLevelMenuInterface
     */
    protected function _getParentMenu()
    {
        return $this->parentMenu;
    }

    /**
     * Sets the parent top-level menu.
     *
     * @since [*next-version*]
     *
     * @param TopLevelMenuInterface $parentMenu
     *
     * @return $this
     */
    protected function _setParentMenu(TopLevelMenuInterface $parentMenu)
    {
        $this->parentMenu = $parentMenu;

        return $this;
    }

    /**
     * Gets the parent ID.
     *
     * Since WordPress menus use their URLs as IDs if they redirect to a URL, this method
     * will check the parent's content type and if found to be a URL string, that string
     * will be the returned as the ID.
     *
     * Otherwise, if the parent content is a callback, its ID property is returned.
     *
     * @since [*next-version*]
     *
     * @return string
     */
    protected function _getParentId()
    {
        $parentMenu    = $this->_getParentMenu();
        $parentContent = $this->_normalizeContent($parentMenu->getContent());

        return filter_var($parentContent, FILTER_VALIDATE_URL)
            ? $parentContent
            : $parentMenu->getId();
    }
}
