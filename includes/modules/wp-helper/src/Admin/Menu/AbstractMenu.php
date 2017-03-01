<?php

namespace RebelCode\Wp\Admin\Menu;

/**
 * Basic functionality of a menu.
 *
 * @since [*next-version*]
 */
abstract class AbstractMenu
{
    /**
     * The menu ID.
     *
     * @since [*next-version*]
     *
     * @var string
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
     * @return string
     */
    protected function _getId()
    {
        return $this->id;
    }

    /**
     * Gets the menu label.
     *
     * @since [*next-version*]
     *
     * @return string
     */
    protected function _getLabel()
    {
        return $this->label;
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
     * Sets the menu ID.
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
     * Sets the menu label.
     *
     * @since [*next-version*]
     *
     * @param string $label
     *
     * @return $this
     */
    protected function _setLabel($label)
    {
        $this->label = $label;

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
}
