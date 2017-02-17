<?php

namespace RebelCode\EddBookings\System\Component;

use \Dhii\App\ComponentInterface as BaseComponentInterface;

/**
 * Something that represents a plugin component.
 *
 * @since [*next-version*]
 */
interface ComponentInterface extends BaseComponentInterface
{
    /**
     * Triggered when the application is ready to begin execution.
     *
     * @since [*next-version*]
     */
    public function onAppReady();

    /**
     * Gets the parent plugin instance.
     *
     * @since [*next-version*]
     *
     * @return PluginInterface The plugin app instance.
     */
    public function getApp();
}
