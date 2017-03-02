<?php

namespace RebelCode\Wp\Admin\Menu;

/**
 * Base implementation of a WordPress menu.
 *
 * @since [*next-version*]
 */
abstract class AbstractBaseMenu extends AbstractMenu implements MenuInterface
{
    /**
     * {@inheritdoc}
     *
     * @since [*next-version*]
     */
    public function getId()
    {
        return $this->_getId();
    }

    /**
     * {@inheritdoc}
     *
     * @since [*next-version*]
     */
    public function getLabel()
    {
        return $this->_getLabel();
    }

    /**
     * {@inheritdoc}
     *
     * @since [*next-version*]
     */
    public function getContent()
    {
        return $this->_getContent();
    }

    /**
     * {@inheritdoc}
     *
     * @since [*next-version*]
     */
    public function getRequiredCapability()
    {
        return $this->_getRequiredCapability();
    }

    /**
     * {@inheritdoc}
     *
     * @since [*next-version*]
     */
    public function register()
    {
        return $this->_register();
    }
}
