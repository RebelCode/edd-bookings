<?php

namespace RebelCode\EddBookings\System\Module;

use \RebelCode\Bookings\Framework\Data\DataReadableInterface;

/**
 * Something that represents an application module.
 *
 * @since [*next-version*]
 */
interface ModuleInterface extends DataReadableInterface
{
    /**
     * Retrieves the module ID.
     *
     * @since [*next-version*]
     *
     * @return string The module ID.
     */
    public function getId();

    /**
     * Retrieves the module name.
     *
     * @since [*next-version*]
     *
     * @return stirng The module name.
     */
    public function getName();

    /**
     * Loads the module.
     *
     * @since [*next-version*]
     */
    public function load();
}
