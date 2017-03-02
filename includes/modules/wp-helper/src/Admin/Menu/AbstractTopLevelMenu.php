<?php

namespace RebelCode\Wp\Admin\Menu;

/**
 * Basic functionality for a top-level menu.
 *
 * @since [*next-version*]
 */
abstract class AbstractTopLevelMenu extends AbstractMenu
{
    /**
     * The icon dashicons name or URL.
     *
     * @since [*next-version*]
     *
     * @var string
     */
    protected $icon;

    /**
     * The menu position.
     *
     * @since [*next-version*]
     *
     * @var int
     */
    protected $position;

    /**
     * Gets the icon to show for this menu.
     *
     * @since [*next-version*]
     *
     * @return string A URL or dashicons icon name.
     */
    protected function _getIcon()
    {
        return $this->icon;
    }

    /**
     * Gets the menu position.
     *
     * @since [*next-version*]
     *
     * @return int
     */
    protected function _getPosition()
    {
        return $this->position;
    }

    /**
     * Sets the icon to show for this menu.
     *
     * @since [*next-version*]
     *
     * @param string $icon A URL or dashicons icon name.
     */
    protected function _setIcon($icon)
    {
        $this->icon = $icon;

        return $this;
    }

    /**
     * Sets the menu position.
     *
     * @since [*next-version*]
     *
     * @param int $position
     *
     * @return $this
     */
    protected function _setPosition($position)
    {
        $this->position = $position;

        return $this;
    }

    /**
     * {@inheritdoc}
     *
     * @since [*next-version*]
     */
    protected function _registerWithCallback($callback)
    {
        return add_menu_page(
            $this->_getPageTitle(),
            $this->_getLabel(),
            $this->_getCapability(),
            $this->_getId(),
            $callback,
            $this->_getIcon(),
            $this->_getPosition()
        );
    }

    /**
     * {@inheritdoc}
     *
     * @since [*next-version*]
     */
    protected function _registerWithUrl($url)
    {
        $hook  = sprintf('menu_%s', $this->_getId());
        $icon  = $this->_getIcon();

        global $menu;

        $menu[$this->_getPosition()] = array(
            $this->_getLabel(),
            $this->_getCapability(),
            $url,
            $this->_getPageTitle(),
            '',
            $hook,
            $icon
        );

        return null;
    }
}
