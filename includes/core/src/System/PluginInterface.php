<?php

namespace RebelCode\EddBookings\System;

use \Dhii\App\AppInterface;
use \Dhii\Di\FactoryInterface;
use \Dhii\Di\WritableContainerInterface;

/**
 * Something that represents a plugin.
 *
 * @since [*next-version*]
 */
interface PluginInterface extends AppInterface
{
    /**
     * Gets the DI container.
     *
     * @since [*next-version*]
     *
     * @return WritableContainerInterface The DI container instance.
     */
    public function getContainer();

    /**
     * Gets the factory.
     *
     * @since [*next-version*]
     *
     * @return FactoryInterface The factory instance.
     */
    public function getFactory();

    /**
     * Retrieves all the components.
     *
     * @since [*next-version*]
     *
     * @return ComponentInterface[] An array of components mapped by their codes.
     */
    public function getComponents();

    /**
     * Triggers execution of the plugin and its components.
     *
     * @since [*next-version*]
     *
     * @return $this This instance.
     */
    public function run();
}
