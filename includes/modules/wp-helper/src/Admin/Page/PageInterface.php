<?php

namespace RebelCode\Wp\Admin\Page;

/**
 * Represents a WordPress admin page.
 *
 * @since [*next-version*]
 */
interface PageInterface
{
    /**
     * Gets the unique ID of the page.
     *
     * @since [*next-version*]
     *
     * @return string
     */
    public function getId();

    /**
     * Gets the title of the page.
     *
     * The title is used by WordPress to set the HTML document title, which browsers
     * show in their window title bar or tab.
     *
     * @since [*next-version*]
     *
     * @return string
     */
    public function getTitle();

    /**
     * Gets the content of the page.
     *
     * @since [*next-version*]
     *
     * @return string
     */
    public function getContent();

    /**
     * Gets the capability that the current user is required to have to view this page.
     *
     * @since [*next-version*]
     *
     * @return string
     */
    public function getRequiredCapability();
}
