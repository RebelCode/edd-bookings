<?php

namespace Aventura\Edd\Bookings\Settings\Section;

use \Aventura\Edd\Bookings\Settings\Node\SettingsNodeInterface;
use \Aventura\Edd\Bookings\Settings\Option\OptionInterface;

/**
 * Describes a settings section, which represents a grouped collection of options.
 *
 * @author Miguel Muscat <miguelmuscat93@gmail.com>
 */
interface SectionInterface extends SettingsNodeInterface
{

    /**
     * Gets an option with a specific ID.
     *
     * @param string $id The ID to search for.
     * @return OptionInterface|null The option instance or null if no option with the given ID exists in this section.
     */
    public function getOption($id);

    /**
     * Gets the options in this section.
     *
     * @return OptionInterface[] An array of option instances.
     */
    public function getOptions();

}
