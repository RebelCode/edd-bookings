<?php

namespace Aventura\Edd\Bookings\Update;

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
     * @param string $previousVersion The previous version.
     */
    public static function update($previousVersion);

}
