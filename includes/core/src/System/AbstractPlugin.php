<?php

namespace RebelCode\EddBookings\System;

use \Dhii\Di\FactoryInterface;
use \Interop\Container\ContainerInterface;
use \RebelCode\EddBookings\System\Component\ComponentInterface;

/**
 * Basic functionality for a plugin.
 *
 * @since [*next-version*]
 */
class AbstractPlugin extends AbstractGenericDiAware
{
    /**
     * The DI container.
     *
     * @since [*next-version*]
     *
     * @var ContainerInterface
     */
    protected $container;

    /**
     * The factory.
     *
     * @since [*next-version*]
     *
     * @var FactoryInterface
     */
    protected $factory;

    /**
     * The components.
     *
     * @since [*next-version*]
     *
     * @var ComponentInterface[]
     */
    protected $components;

    /**
     * {@inheritdoc}
     *
     * @since [*next-version*]
     */
    protected function _getContainer()
    {
        return $this->container;
    }

    /**
     * {@inheritdoc}
     *
     * @since [*next-version*]
     */
    protected function _getFactory()
    {
        return $this->factory;
    }

    /**
     * Sets the DI container.
     *
     * @since [*next-version*]
     *
     * @param ContainerInterface $container The new DI container instance.
     *
     * @return $this This instance.
     */
    protected function _setContainer(ContainerInterface $container)
    {
        $this->container = $container;

        return $this;
    }

    /**
     * Sets the factory.
     *
     * @since [*next-version*]
     *
     * @param FactoryInterface $factory The new factory instance.
     *
     * @return $this This instance.
     */
    protected function _setFactory(FactoryInterface $factory)
    {
        $this->factory = $factory;

        return $this;
    }

    /**
     * Retrieves all the components.
     *
     * @since [*next-version*]
     *
     * @return ComponentInterface[] An array of components mapped by their codes.
     */
    protected function _getComponents()
    {
        return $this->components;
    }

    /**
     * {@inheritdoc}
     *
     * @since [*next-version*]
     */
    protected function _hasComponent($code)
    {
        return isset($this->components[$code]);
    }

    /**
     * Retrieve an component by code.
     *
     * @since [*next-version*]
     *
     * @return ComponentInterface
     */
    protected function _getComponent($code)
    {
        if (!$this->hasComponent($code)) {
            $this->registerComponent($this->getFactory()->make($code), $code);
        }

        return $this->components[$code];
    }

    /**
     * Register a component with this instance.
     *
     * @since [*next-version*]
     *
     * @param ComponentInterface $component The component to register.
     * @param string             $code      The code to register the component with.
     *
     * @throws RuntimeException If a component with the specified code is already registered.
     *
     * @return PluginInterface This instance.
     */
    protected function _registerComponent(ComponentInterface $component, $code)
    {
        $this->components[$code] = $component;

        return $this;
    }

    /**
     * Removes all the components from this instance.
     *
     * @since [*next-version*]
     *
     * @return $this This instance.
     */
    protected function _resetComponents()
    {
        $this->components = array();

        return $this;
    }
}
