<?php

namespace RebelCode\EddBookings\System\Component;

use \Dhii\App\AppInterface;

/**
 * Base component class - for convenience when extended by concrete components.
 *
 * @since [*next-version*]
 */
abstract class AbstractBaseComponent extends AbstractComponent implements ComponentInterface
{
    /**
     * Constructor.
     *
     * @param AppInterface $app The parent app instance.
     */
    public function __construct(AppInterface $app)
    {
        $this->_setApp($app);
    }

    /**
     * {@inheritdoc}
     *
     * @since [*next-version*]
     */
    public function getApp()
    {
        return $this->_getApp();
    }

    /**
     * Returns a callable for a specific method.
     *
     * @since [*next-version*]
     *
     * @param string $method The method name.
     *
     * @return callable The callable for the method with the given name.
     */
    protected function _callback($method)
    {
        return array($this, $method);
    }
}
