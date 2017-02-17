<?php

namespace RebelCode\EddBookings\System\ModuleLoader;

/**
 * Something that can load modules.
 *
 * @since [*next-version*]
 */
interface ModuleLoaderInterface
{
    /**
     * Loads the module at the given
     *
     * @since [*next-version*]
     *
     * @param string $filepath The path to the file that represents the module.
     *
     * @return ModuleInterface The loaded module.
     */
    public function loadModule($filepath);

    /**
     * Gets the loaded modules.
     *
     * @since [*next-version*]
     *
     * @return ModuleInterface[] An array of loaded modules mapped by their IDs.
     */
    public function getLoadedModules();
}
