<?php

namespace Aventura\Edd\Bookings\Controller;

use \Aventura\Edd\Bookings\Plugin;

/**
 * Generic controller interface.
 * 
 * @author Miguel Muscat <miguelmuscat93@gmail.com>
 */
interface ControllerInterface
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
     * @param Plugin $plugin The parent plugin instance.
     * @return ControllerInterface This instance.
     */
    public function setPlugin(Plugin $plugin);
    
    /**
     * Registers the WordPress hooks.
     */
    public function hook();

}
