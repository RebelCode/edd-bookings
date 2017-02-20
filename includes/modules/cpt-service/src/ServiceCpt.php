<?php

namespace RebelCode\EddBookings\CustomPostType\Service;

use \RebelCode\EddBookings\CustomPostType;

/**
 * Service custom post type.
 *
 * @since [*next-version*]
 */
class ServiceCpt extends CustomPostType
{
    /**
     * {@inheritdoc}
     *
     * @since [*next-version*]
     *
     * @internal Overrides to prevent re-registration of the EDD `download` CPT
     */
    public function onAppReady()
    {
        // Do nothing
    }
}
