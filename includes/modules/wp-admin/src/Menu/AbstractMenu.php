<?php

namespace RebelCode\WordPress\Admin\Menu;

use \RebelCode\WordPress\Admin\Page;

/**
 * Basic functionality for a WordPress menu.
 *
 * @since [*next-version*]
 */
abstract class AbstractMenu implements MenuInterface
{
    /**
     * The menu ID.
     *
     * @since [*next-version*]
     *
     * @var int
     */
    protected $id;

    /**
     * The menu label.
     *
     * @since [*next-version*]
     *
     * @var string
     */
    protected $label;

    /**
     * The page for this menu.
     *
     * @var Page
     */
    protected $page;

    /**
     * The menu position.
     *
     * @since [*next-version*]
     *
     * @var int
     */
    protected $position;

    /**
     * Gets the menu ID.
     *
     * @since [*next-version*]
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Gets the menu label.
     *
     * @since [*next-version*]
     *
     * @return string The menu label.
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * Gets the menu page.
     *
     * @since [*next-version*]
     *
     * @return Page The menu page instance.
     */
    public function getPage()
    {
        return $this->page;
    }

    /**
     * Gets the menu position.
     *
     * @since [*next-version*]
     *
     * @return int The menu position.
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * Sets the menu id.
     *
     * @since [*next-version*]
     *
     * @param int $id The menu ID.
     *
     * @return $this This instance.
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Sets the menu label.
     *
     * @since [*next-version*]
     *
     * @param string $label The menu label.
     *
     * @return $this This instance.
     */
    public function setLabel($label)
    {
        $this->label = $label;

        return $this;
    }

    /**
     * Sets the menu page.
     *
     * @since [*next-version*]
     *
     * @param Page $page The page instance.
     *
     * @return $this This instance.
     */
    public function setPage($page)
    {
        $this->page = $page;

        return $this;
    }

    /**
     * Sets the menu position.
     *
     * @since [*next-version*]
     *
     * @param int $position The menu position.
     *
     * @return $this This instance.
     */
    public function setPosition($position)
    {
        $this->position = $position;

        return $this;
    }
}
