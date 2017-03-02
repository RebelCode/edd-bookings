<?php

namespace RebelCode\Wp\Admin\Page;

/**
 * Base abstract class for a page - primarily used for extending.
 *
 * @since [*next-version*]
 */
abstract class AbstractBasePage extends AbstractPage implements PageInterface
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
    public function getTitle()
    {
        return $this->_getTitle();
    }

    /**
     * {@inheritdoc}
     *
     * @since [*next-version*]
     */
    public function getCapability()
    {
        return $this->_getCapability();
    }

    /**
     * {@inheritdoc}
     *
     * @since [*next-version*]
     *
     * @return $this
     */
    public function __invoke()
    {
        echo $this->getContent();

        return $this;
    }

    /**
     * {@inheritdoc}
     *
     * @since [*next-version*]
     */
    public function __toString()
    {
        return (string) $this->getContent();
    }
}
