<?php

namespace RebelCode\Wp\Admin\Menu;

use RebelCode\EddBookings\Block\BlockInterface;

/**
 * Basic functionality for a top-level menu.
 *
 * @since [*next-version*]
 */
abstract class AbstractTopLevelMenu extends AbstractMenu
{
    /**
     * The HTML class for top level menus.
     *
     * @since [*next-version*]
     */
    const MENU_HTML_CLASS = 'menu-top';

    /**
     * {@inheritdoc}
     *
     * @since [*next-version*]
     */
    protected function _register()
    {
        $content = $this->_normalizeContent($this->_getContent());

        $method = is_callable($content)
            ? '_registerWithCallback'
            : '_registerWithUrl';

        return $this->$method(
            $this->_getId(),
            $this->_getLabel(),
            $this->_getPageTitle(),
            $this->_getIcon(),
            $content,
            $this->_getRequiredCapability(),
            $this->_getPosition()
        );
    }

    /**
     * Creates a render callback function for the given string-like content.
     *
     * @since [*next-version*]
     *
     * @param BlockInterface|string $content The content to output from the callback.
     *
     * @return callable
     */
    protected function _createRenderCallback($content)
    {
        return function() use ($content) {
            echo $content;
        };
    }

    /**
     * Registers the menu when using a callback.
     *
     * @since [*next-version*]
     *
     * @global array $menu The WordPress global menu array.
     *
     * @param string $menuId The menu ID.
     * @param string $menuLabel The menu label.
     * @param string $pageTitle The page title.
     * @param string $icon The menu icon dashicon name or URL.
     * @param string $callback The callback that renders the content.
     * @param string $capability The required capability.
     * @param string $position The menu position.
     *
     * @return string The name of the event triggered when this menu is selected.
     */
    protected function _registerWithCallback(
        $menuId,
        $menuLabel,
        $pageTitle,
        $icon,
        $callback,
        $capability,
        $position
    ) {
        return add_menu_page(
            $pageTitle,
            $menuLabel,
            $capability,
            $menuId,
            $callback,
            $icon,
            $position
        );
    }

    /**
     * Registers the menu manually, to set the menu "slug" index with the URL.
     *
     * @since [*next-version*]
     *
     * @global array $menu The WordPress global menu array.
     *
     * @param string $menuId The menu ID.
     * @param string $menuLabel The menu label.
     * @param string $pageTitle The page title.
     * @param string $icon The menu icon dashicon name or URL.
     * @param string $url The URL to redirect to.
     * @param string $capability The required capability.
     * @param string $position The menu position.
     *
     * @return string The name of the event triggered when this menu is selected.
     */
    protected function _registerWithUrl(
        $menuId,
        $menuLabel,
        $pageTitle,
        $icon,
        $url,
        $capability,
        $position
    ) {
        $eventName = sprintf('menu_%s', $menuId);

        global $menu;
        $menu[$position] = array(
            $menuLabel,
            $capability,
            $url,
            $pageTitle,
            sprintf('%1$s %2$s %2$s', static::MENU_HTML_CLASS, $icon, $eventName),
            $eventName,
            $icon
        );

        return null;
    }
}
