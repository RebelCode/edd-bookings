<?php

namespace RebelCode\EddBookings\Controller;

use \RebelCode\Storage\WordPress\AbstractCptController;

/**
 * A booking model controller.
 *
 * Responsible for fetching bookings from the WordPress database and creating model instances.
 *
 * @since [*next-version*]
 */
class BookingController extends AbstractCptController
{
    /**
     * {@inheritdoc}
     *
     * @since [*next-version*]
     */
    protected function _createModel($id)
    {
        $booking = $this->getFactory()->make('booking', array(
            'id' => $id
        ));

        return $booking;
    }
}
