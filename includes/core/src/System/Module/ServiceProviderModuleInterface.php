<?php

namespace RebelCode\EddBookings\System\Module;

use Interop\Container\ServiceProvider;

/**
 * Represents a module that provides a service provider.
 *
 * @since [*next-version*]
 */
interface ServiceProviderModuleInterface extends ModuleInterface
{
    /**
     * Gets the module's service provider.
     *
     * @since [*next-version*]
     *
     * @return ServiceProvider
     */
    public function getServiceProvider();
}
