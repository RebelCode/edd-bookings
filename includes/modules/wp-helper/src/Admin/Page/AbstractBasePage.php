<?php

namespace RebelCode\Wp\Admin\Page;

/**
 * Base abstract class for a page - primarily used for extending.
 *
 * @since [*next-version*]
 */
abstract class AbstractBasePage extends AbstractBlockPage implements
    BlockPageInterface,
    CallbackPageInterface
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
     *
     * @return $this
     */
    public function __invoke()
    {
        echo $this->_getContent();

        return $this;
    }

    /**
     * {@inheritdoc}
     *
     * @since [*next-version*]
     */
    public function __toString()
    {
        return $this->_toString();
    }
}
