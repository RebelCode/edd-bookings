<?php

namespace RebelCode\EddBookings\System\Migration;

/**
 * Something that can perform data migration for system updates.
 *
 * @since [*next-version*]
 */
interface MigraterInterface
{
    /**
     * Performs a migration from one version to another.
     *
     * @since [*next-version*]
     *
     * @param string $from The version to migrate from.
     * @param string $to   The version to migrate to.
     */
    public function migrate($from, $to);
}
