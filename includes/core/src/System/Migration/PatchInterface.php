<?php

namespace RebelCode\EddBookings\System\Migration;

/**
 * Something that represents a migration patch.
 *
 * @since [*next-version*]
 */
interface PatchInterface
{
    /**
     * Applies this patch.
     *
     * @since [*next-version*]
     *
     * @throws PatchException If an error occurs while applying the patch.
     */
    public function apply();
}
