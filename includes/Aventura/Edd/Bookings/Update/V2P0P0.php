<?php

namespace Aventura\Edd\Bookings\Update;

use \Aventura\Edd\Bookings\Plugin;

/**
 * Update class for version 2.0.0
 *
 * @author Miguel Muscat <miguelmuscat93@gmail.com>
 */
class V2P0P0 implements UpdateInterface
{
    
    /**
     * {@inheritdoc}
     */
    public static function update(Plugin $plugin)
    {
        // By simply querying the Downloads, the controller will also fetch their meta and pass it onto the factory
        // to create instances. The factory will detect the legacy meta and perform the required conversion.
        $plugin->getServiceController()->query();
        // Return true to signify success
        return true;
    }

}
