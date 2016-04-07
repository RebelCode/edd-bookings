<?php

namespace Aventura\Edd\Bookings\Patch;

use \Aventura\Edd\Bookings\Plugin;

/**
 * Patch class for version 2.0.0
 *
 * @author Miguel Muscat <miguelmuscat93@gmail.com>
 */
class V2_0_0 implements PatchInterface
{
    
    /**
     * {@inheritdoc}
     */
    public static function apply(Plugin $plugin)
    {
        // By simply querying the Downloads, the controller will also fetch their meta and pass it onto the factory
        // to create instances. The factory will detect the legacy meta and perform the required conversion.
        $plugin->getServiceController()->query();
        // The same applies for bookings
        $plugin->getBookingController()->query();
        // Return true to signify success
        return true;
    }

}
