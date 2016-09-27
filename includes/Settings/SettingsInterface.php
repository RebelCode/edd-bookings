<?php

namespace Aventura\Edd\Bookings\Settings;

use \Aventura\Edd\Bookings\Plugin;
use \Aventura\Edd\Bookings\Settings\Database\DatabaseInterface;
use \Aventura\Edd\Bookings\Settings\Section\SectionInterface;

/**
 * Any object that serves as a settings controller.
 *
 * @author Miguel Muscat <miguelmuscat93@gmail.com>
 */
interface SettingsInterface
{

    /**
     * Gets the database controller for retrieving records.
     *
     * @return DatabaseInterface The database controller instance.
     */
    public function getDatabase();

    /**
     * Gets the sections.
     *
     * @return SectionInterface[] An array of section instances.
     */
    public function getSections();

    /**
     * Gets the parent plugin instance.
     *
     * @return Plugin The plugin instance.
     */
    public function getPlugin();
}
