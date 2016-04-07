<?php
namespace Aventura\Edd\Bookings\Integration;

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
     * Registers the WordPress hooks.
     */
    public function hook();

}
