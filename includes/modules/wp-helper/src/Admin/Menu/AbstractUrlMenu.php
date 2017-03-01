<?php

namespace RebelCode\Wp\Admin\Menu;

/**
 * Basic functionality for a menu that redirects to a URL.
 *
 * @since [*next-version*]
 */
abstract class AbstractUrlMenu extends AbstractMenu
{
    /**
     * The URL.
     *
     * @since [*next-version*]
     *
     * @var string
     */
    protected $url;

    /**
     * Gets the URL that this menu redirects to.
     *
     * @since [*next-version*]
     *
     * @return string
     */
    protected function _getUrl()
    {
        return $this->url;
    }

    /**
     * Sets the URL that this menu redirects to.
     *
     * @since [*next-version*]
     *
     * @param string $url
     *
     * @return $this
     */
    protected function _setUrl($url)
    {
        $this->url = $url;

        return $this;
    }
}
