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
     * The parent top-level menu instance or ID.
     *
     * @since [*next-version*]
     *
     * @var TopLevelMenuInterface|string
     */
    protected $parentMenu;

    /**
     * Gets the parent top-level menu.
     *
     * @since [*next-version*]
     *
     * @return TopLevelMenuInterface|string The parent menu instance or ID.
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
     * @param TopLevelMenuInterface|string $parentMenu The parent menu instance or ID.
     *
     * @return $this
     */
    protected function _setParentMenu($parentMenu)
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
        $parentMenu = $this->_getParentMenu();

        if (is_string($parentMenu)) {
            return $parentMenu;
        }

        $parentContent = $this->_normalizeContent($parentMenu->getContent());

        return filter_var($parentContent, FILTER_VALIDATE_URL)
            ? $parentContent
            : $parentMenu->getId();
    }

    /**
     * {@inheritdoc}
     *
     * @since [*next-version*]
     */
    protected function _registerWithCallback($callback) {
        return add_submenu_page(
            $this->_getParentId(),
            $this->_getPageTitle(),
            $this->_getLabel(),
            $this->_getRequiredCapability(),
            $this->_getId(),
            $callback
        );
    }

    /**
     * {@inheritdoc}
     *
     * @since [*next-version*]
     */
    protected function _registerWithUrl($url) {
        global $submenu;

        $submenu[$this->_getParentId()] = array(
            $this->_getLabel(),
            $this->_getRequiredCapability(),
            $url,
            $this->_getPageTitle()
        );

        return null;
    }
}
