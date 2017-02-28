<?php

namespace RebelCode\Wp\Admin\Page;

/**
 * Basic functionality of a WordPress admin page.
 *
 * @since [*next-version*]
 */
abstract class AbstractPage
{
    /**
     * The ID of the page.
     *
     * @since [*next-version*]
     *
     * @var string
     */
    protected $id;

    /**
     * The title of the page.
     *
     * @since [*next-version*]
     *
     * @var string
     */
    protected $title;

    /**
     * The user capability required to view this page.
     *
     * @since [*next-version*]
     *
     * @var string
     */
    protected $requiredCapability;

    /**
     * Gets the content for this page.
     *
     * @since [*next-version*]
     *
     * @return string
     */
    abstract protected function _getContent();

    /**
     * Gets the ID of the page.
     *
     * @since [*next-version*]
     *
     * @return string
     */
    protected function _getId()
    {
        return $this->id;
    }

    /**
     * Gets the title of the page.
     *
     * @since [*next-version*]
     *
     * @return string
     */
    protected function _getTitle()
    {
        return $this->title;
    }

    /**
     * Gets the capability that the user is required to have to view this page.
     *
     * @since [*next-version*]
     *
     * @return string
     */
    protected function _getRequiredCapability()
    {
        return $this->requiredCapability;
    }

    /**
     * Sets the ID of the page.
     *
     * @since [*next-version*]
     *
     * @param string $id
     *
     * @return $this
     */
    protected function _setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Sets the title of the page.
     *
     * @since [*next-version*]
     *
     * @param string $title
     *
     * @return $this
     */
    protected function _setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Sets the capability that the user is required to have to view this page.
     *
     * @since [*next-version*]
     *
     * @param string $requiredCapability
     *
     * @return $this
     */
    protected function _setRequiredCapability($requiredCapability)
    {
        $this->requiredCapability = $requiredCapability;

        return $this;
    }
}
