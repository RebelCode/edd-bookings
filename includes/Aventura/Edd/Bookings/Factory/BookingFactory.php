<?php

namespace Aventura\Edd\Bookings\Factory;

use \Aventura\Diary\DateTime;
use \Aventura\Diary\DateTime\Duration;
use \Aventura\Edd\Bookings\Booking;
use \Aventura\Edd\Bookings\CustomPostType\BookingPostType;

/**
 * Factory class for Booking instances and the CustomPostType instance.
 *
 * @author Miguel Muscat <miguelmuscat93@gmail.com>
 */
class BookingFactory extends ModelCptFactoryAbstract
{

    /**
     * {@inheritdoc}
     */
    const DEFAULT_CLASSNAME = 'Aventura\\Edd\\Bookings\\Booking';

    /**
     * Creates the booking CPT.
     * 
     * @param array $data Optional array of data. Default: array()
     * @return BookingPostType The created instance.
     */
    public function createCpt(array $data = array())
    {
        return new BookingPostType($this->getPlugin());
    }
    
    /**
     * {@inheritdoc}
     * 
     * @param array $arg An array of data to use for creating the instance.
     * @return Booking
     */
    public function create(array $arg)
    {
        if (!isset($arg['id'])) {
            $booking = null;
        } else {
            $data = \wp_parse_args($arg, array(
                'start' => null,
                'duration' => null,
                'service_id' => null,
                'customer_id' => null,
                'payment_id' => null,
                'client_timezone' => 0
            ));
            $className = $this->getClassName();
            // Prepare start and duration instances
            $start = new DateTime($data['start']);
            $duration = new Duration($data['duration']);
            /* @var $booking Booking */
            $booking = new $className($data['id'], $start, $duration, $data['service_id']);
            $booking->setPaymentId($data['payment_id'])
                    ->setCustomerId($data['customer_id'])
                    ->setClientTimezone($data['client_timezone']);
        }
        return $booking;
    }

}
