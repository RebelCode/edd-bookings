<?php

namespace RebelCode\EddBookings\System\Module;

use RebelCode\EddBookings\System\Component\ComponentInterface;
use Traversable;

/**
 * Represents a module that can provide components.
 *
 * @since [*next-version*]
 */
interface ComponentProviderModuleInterface
{
    /**
     * Gets the components provided by this module.
     *
     * @since [*next-version*]
     *
     * @return ComponentInterface[]|Traversable
     */
    public function getComponents();
}
