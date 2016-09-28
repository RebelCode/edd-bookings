<?php

namespace Aventura\Edd\Bookings\Settings;

use \Aventura\Edd\Bookings\Settings\Database\Record\RecordInterface;
use \Aventura\Edd\Bookings\Settings\Section\SectionInterface;

/**
 * Any object that serves as a settings controller.
 *
 * @author Miguel Muscat <miguelmuscat93@gmail.com>
 */
interface SettingsInterface
{

    /**
     * Gets the database record that contains the settings data..
     *
     * @return RecordInterface The record instance.
     */
    public function getRecord();

    /**
     * Gets the sections.
     *
     * @return SectionInterface[] An array of section instances.
     */
    public function getSections();

}
