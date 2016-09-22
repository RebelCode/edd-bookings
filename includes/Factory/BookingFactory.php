<?php

namespace Aventura\Edd\Bookings\Factory;

use \Aventura\Diary\DateTime;
use \Aventura\Diary\DateTime\Duration;
use \Aventura\Edd\Bookings\Model\Booking;
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
    const DEFAULT_CLASSNAME = 'Aventura\\Edd\\Bookings\\Model\\Booking';

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
     * @param array $args An array of data to use for creating the instance.
     * @return Booking
     */
    public function create(array $args)
    {
        if (!isset($args['id'])) {
            $booking = null;
        } else {
            $didNormalize = isset($args['edd_bk_session_unit']);
            $normalized = $this->maybeNormalizeLegacyMeta($args);
            $data = \wp_parse_args($normalized, array(
                'start' => null,
                'duration' => null,
                'service_id' => null,
                'customer_id' => null,
                'payment_id' => null,
                'client_timezone' => 0
            ));
            $className = $this->getClassName();
            // Prepare start and duration instances
            $start = new DateTime(intval($data['start']));
            $duration = new Duration(intval($data['duration']));
            /* @var $booking Booking */
            $booking = new $className($data['id'], $start, $duration, $data['service_id']);
            $booking->setPaymentId($data['payment_id'])
                    ->setCustomerId($data['customer_id'])
                    ->setClientTimezone($data['client_timezone']);
            // If the legacy data was normalized, save the new normalized meta to prevent further normalization.
            if ($didNormalize) {
                $meta = $data;
                unset($meta['id']);
                $this->getPlugin()->getBookingController()->saveMeta($data['id'], $meta);
            }
        }
        return $booking;
    }

    /**
     * Normalizes the meta into the expected format, if the legacy meta structure is detected.
     * 
     * @param array $meta The meta to normalize.
     * @return array The normalized meta.
     * @throws \Exception If an unknown session unit is encountered in the meta.
     */
    public function maybeNormalizeLegacyMeta($meta)
    {
        // Copy the array
        $normalized = $meta;
        // Check for the session unit, which is no longer used
        if (isset($normalized['edd_bk_session_unit'])) {
            // Get the session unit and remove it
            $sessionUnit = $normalized['edd_bk_session_unit'];
            unset($normalized['edd_bk_session_unit']);

            // Create the start timestamp from the date and time
            $normalized['start'] = intval($normalized['edd_bk_date']);
            if (isset($normalized['edd_bk_time'])) {
                $normalized['start'] += intval($normalized['edd_bk_time']);
            }

            // Duration was previously in terms on sessions. Turn it into seconds
            if (method_exists('Aventura\\Diary\\DateTime\\Duration', $sessionUnit)) {
                $normalized['duration'] = call_user_func_array(
                        array('Aventura\\Diary\\DateTime\\Duration', $sessionUnit),
                        array($normalized['edd_bk_duration'], false));
            } else {
                throw new Exception(sprintf('Encountered unknown session unit: %s', $sessionUnit));
            }

            $normalized['service_id'] = $meta['edd_bk_service_id'];
            $normalized['customer_id'] = $meta['edd_bk_customer_id'];
            $normalized['payment_id'] = $meta['edd_bk_payment_id'];
            $normalized['client_timezone'] = Duration::hours(intval($meta['edd_bk_timezone_offset']), false);
        }
        return $normalized;
    }
    
}
