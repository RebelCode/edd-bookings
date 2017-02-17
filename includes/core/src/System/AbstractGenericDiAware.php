<?php

namespace RebelCode\EddBookings\System;

use \Dhii\Di\FactoryInterface;
use \Interop\Container\ContainerInterface;
use \Interop\Container\Exception\ContainerException;
use \Interop\Container\Exception\NotFoundException;

/**
 * Something that provides shortcut methods for dependency injection.
 *
 * @since [*next-version*]
 */
abstract class AbstractGenericDiAware
{
    /**
     * Gets the DI container.
     *
     * @since [*next-version*]
     *
     * @return ContainerInterface The DI container instance.
     */
    abstract protected function _getContainer();

    /**
     * Gets the factory.
     *
     * @since [*next-version*]
     *
     * @return FactoryInterface The factory instance.
     */
    abstract protected function _getFactory();

    /**
     * Gets the DI container or a service from the DI container.
     *
     * @since [*next-version*]
     *
     * @param string $serviceId [optional] The service ID. Default: null
     *
     * @return ContainerInterface|mixed If $serviceId is null, the DI container.
     *                                  Otherwise, the service with the given ID.
     *
     * @throws NotFoundException If $serviceId is not null and not service exists with that ID.
     * @throws ContainerException If an error occurred while creating the service instance.
     */
    public function di($serviceId = null)
    {
        return is_null($serviceId)
            ? $this->_getContainer()
            : $this->_getContainer()->get($serviceId);
    }

    /**
     * Gets the factory or creates a new service instance.
     *
     * @since [*next-version*]
     *
     * @param string $serviceId [optional] The service ID. Default: null
     * @param array  $config    [optional] An array of configuration values. Default: array()
     *
     * @return FactoryInterface|mixed If $serviceId is null, the factory.
     *                                Otherwise, an instance of the service with the given ID.
     *
     * @throws NotFoundException If $serviceId is not null and not service exists with that ID.
     * @throws ContainerException If an error occurred while creating the service instance.
     */
    public function factory($serviceId = null, array $config = array())
    {
        return is_null($serviceId)
            ? $this->_getFactory()
            : $this->_getFactory()->make($serviceId, $config);
    }
}
