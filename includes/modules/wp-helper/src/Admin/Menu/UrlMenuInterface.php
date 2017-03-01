<?php

namespace RebelCode\Wp\Admin\Menu;

/**
 * Represents a WordPress menu that redirects to a URL.
 *
 * @since [*next-version*]
 */
interface UrlMenuInterface extends MenuInterface
{
    /**
     * Gets the URL that the menu redirects to.
     *
     * @since [*next-version*]
     *
     * @return string
     */
    public function getUrl();
}
