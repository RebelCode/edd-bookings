<?php

namespace RebelCode\EddBookings\System\Module;

/**
 * Represents a module that uses a script file that gets executed during load.
 *
 * @since [*next-version*]
 */
interface FileModuleInterface extends ModuleInterface
{
    /**
     * Retrieves the module directory.
     *
     * @since [*next-version*]
     *
     * @return string The absolute path to the module directory.
     */
    public function getDirectory();

    /**
     * Retrieves the path to the module file.
     *
     * @since [*next-version*]
     *
     * @return string The absolute path to the module file.
     */
    public function getFile();
}
