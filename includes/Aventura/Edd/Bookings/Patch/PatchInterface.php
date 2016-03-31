<?php

namespace Aventura\Edd\Bookings\Patch;

use \Aventura\Edd\Bookings\Plugin;

/**
 * Generic definition of a patch.
 * 
 * @author Miguel Muscat <miguelmuscat93@gmail.com>
 */
interface PatchInterface
{
    
    /**
     * Applies the patch.
     * 
     * @param Plugin $plugin The plugin instance.
     */
    public static function apply(Plugin $plugin);

}
