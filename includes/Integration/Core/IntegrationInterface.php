<?php

namespace Aventura\Edd\Bookings\Integration\Core;

use \Aventura\Edd\Bookings\Plugin;

/**
 * Generic definition of an object that is used to integrate this plugin with another.
 * 
 * @author Miguel Muscat <miguelmuscat93@gmail.com>
 */
interface IntegrationInterface
{

    /**
     * Gets the parent plugin instance.
     * 
     * @return Plugin
     */
    public function getPlugin();
    
    /**
     * Sets the parent plugin instance.
     * 
     * @param Plugin $plugin The new parent plugin instance.
     * @return IntegrationInterface This instane.
     */
    public function setPlugin(Plugin $plugin);

    /**
     * Registers the WordPress hooks.
     */
    public function hook();

}
