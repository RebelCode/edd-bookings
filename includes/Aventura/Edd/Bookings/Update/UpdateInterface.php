<?php

namespace Aventura\Edd\Bookings\Update;

use \Aventura\Edd\Bookings\Plugin;

/**
 * Generic definition of an update.
 * 
 * @author Miguel Muscat <miguelmuscat93@gmail.com>
 */
interface UpdateInterface
{
    
    /**
     * Performs the updater procedure.
     * 
     * @param Plugin $plugin The plugin instance.
     */
    public static function update(Plugin $plugin);

}
