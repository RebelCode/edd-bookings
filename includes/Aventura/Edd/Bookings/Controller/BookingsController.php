<?php

namespace Aventura\Edd\Bookings\Controller;

use \Aventura\Diary\DateTime;
use \Aventura\Diary\DateTime\Period;
use \Aventura\Edd\Bookings\Model\Booking;
use \Aventura\Edd\Bookings\CustomPostType\BookingPostType;
use \Aventura\Edd\Bookings\Factory\ModelCptFactoryAbstract;
use \Aventura\Edd\Bookings\Plugin;
use \Exception;

/**
 * Bookings controller.
 *
 * @author Miguel Muscat <miguelmuscat93@gmail.com>
 */
class BookingController extends ControllerAbstract
{
    
    /**
     * Constructs a new instance.
     * 
     * @param Plugin $plugin The parent plugin instance.
     */
    public function __construct(Plugin $plugin, ModelCptFactoryAbstract $factory)
    {
        parent::__construct($plugin, $factory);
    }
    
    /**
     * Gets the booking CPT.
     * 
     * @return BookingPostType The booking CPT instance.
     */
    public function getPostType()
    {
        if (is_null($this->_cpt)) {
            $this->_cpt = $this->getFactory()->createCpt();
        }
        return $this->_cpt;
    }
    
    /**
     * Gets the mapping of factory data keys to post meta keys.
     * 
     * @return array An assoc array with factory data keys as array keys and post meta keys as array values.
     */
    public function getMetaMapping()
    {
        return \apply_filters('edd_bk_booking_meta_mapping', array(
            'start' => 'edd_bk_start',
            'duration' => 'edd_bk_duration',
            'service_id' => 'edd_bk_service_id',
            'payment_id' => 'edd_bk_payment_id',
            'customer_id' => 'edd_bk_customer_id'
        ));
    }

    /**
     * Normalizes the meta into the expected format, if the legacy meta structure is detected.
     * 
     * @param array $meta The meta to normalize.
     * @return array The normalized meta.
     */
    public function normalizeLegacyMeta($meta)
    {
        // Copy the array
        $normalized = $meta;
        // Check for the session unit, which is no longer used
        if (isset($normalized['edd_bk_session_unit'])) {
            // Get the session unit and remove it
            $sessionUnit = $normalized['session_unit'];
            unset($normalized['session_unit']);

            // Create the start timestamp from the date and time
            $normalized['edd_bk_start'] = intval($normalized['edd_bk_date']);
            if (isset($normalized['edd_bk_time'])) {
                $normalized['edd_bk_start'] += intval($normalized['edd_bk_time']);
            }

            // Duration was previously in terms on sessions. Turn it into seconds
            if (method_exists('Aventura\\Diary\\DateTime\\Duration', $sessionUnit)) {
                $normalized['duration'] = call_user_func_array(
                        array('Aventura\\Diary\\DateTime\\Duration', $sessionUnit), array($normalized['duration']), false);
            } else {
                throw new Exception(sprintf('Encountered unknown session unit: %s', $sessionUnit));
            }
        }
        return $normalized;
    }

    /**
     * Gets the booking with the given ID.
     * 
     * @param integer $id The ID of the booking.
     * @return Booking
     */
    public function get($id)
    {
        if (\get_post($id) === false) {
            $booking = null;
        } else {
            // Get all custom meta fields for the post
            $unnormalizedMeta = \get_post_custom($id);
            // Normalize meta - if detected legacy meta structure, normalize
            $meta = (isset($meta['edd_bk_session_unit'])) ? $this->normalizeLegacyMeta($unnormalizedMeta) : $unnormalizedMeta;
            // Create the data array, from the mapping.
            $data = array();
            foreach ($this->getMetaMapping() as $dataKey => $metaKey) {
                if (isset($meta[$metaKey])) {
                    $data[$dataKey] = $meta[$metaKey];
                }
            }
            // Add the ID
            $data['id'] = $id;
            // Create the booking
            $booking = $this->getBookingFactory()->create($data);
        }
        return $booking;
    }

    /**
     * Gets the bookings from the DB.
     * 
     * This is a generic query method that gets all bookings by default, but becomes more specific when using the
     * parameter.
     * 
     * @param array $metaQueries Optional, default: array(). An array of meta queries.
     * @return array All the saved bookings, or the bookings that match the given meta queries.
     */
    public function query($metaQueries = array())
    {
        $args = array(
            'post_type' => BookingPostType::SLUG,
            'post_status' => 'publish',
            'meta_query' => $metaQueries
        );
        $filtered = \apply_filters('edd_bk_query_bookings', $args);
        // Submit query and compile array of bookings
        $query = new \WP_Query($filtered);
        $bookings = array();
        while ($query->have_posts()) {
            $query->the_post();
            $bookings[] = $this->get(\get_the_ID());
        }
        // Reset WordPress' query data and return array
        \wp_reset_postdata();
        return $bookings;
    }

    /**
     * Gets all the bookings for a single service.
     * 
     * @param string|id $id The ID of the service.
     * @param Period $period (Optional) If given, the function will return only bookings for the service that start
     *                       in this period's range. Default: null
     * @return array An array of Booking instances.
     */
    public function getBookingsForService($id, $period = null)
    {
        if (\get_post($id) === false) {
            throw new Exception(sprintf('Service with ID #%s does not exist!', $id));
        }
        // Prepare query args
        $metaQueries = array();
        $metaQueries[] = array(
            'key' => 'edd_bk_service_id',
            'value' => strval($id),
            'compare' => '='
        );
        // Add date query if period is given
        if ($period !== null) {
            $metaQueries[] = array(
                'key' => 'edd_bk_start',
                'value' => array($period->getStart()->getTimestamp(), $period->getEnd()->getTimestamp()),
                'compare' => 'BETWEEN'
            );
            $metaQueries['relation'] = 'AND';
        }
        $filtered = \apply_filters('edd_bk_query_bookings_for_service', $metaQueries, $id);
        return $this->query($filtered);
    }

    /**
     * Gets all the bookings for a particular payment.
     * 
     * @param integer $id The ID of the payment.
     * @return array An array of EDD_BK_Booking instances.
     */
    public function getBookingsForPayemnt($id)
    {
        if (\get_post($id) === false) {
            throw new Exception(sprintf('Service with ID #%s does not exist!', $id));
        }
        $metaQueries = array();
        $metaQueries[] = array(
            'key' => 'edd_bk_payment_id',
            'value' => strval($id),
            'compare' => '='
        );
        $filtered = \apply_filters('edd_bk_query_bookings_for_payment', $metaQueries, $id);
        return $this->query($filtered);
    }

    /**
     * Saves a booking into the database.
     * 
     * @param Booking $booking The booking instance.
     * @return integer The ID of the booking.
     * @throws Exception If an error is encountered while trying to insert into the database.
     */
    public function saveBooking(Booking $booking)
    {
        $id = $booking->getId();
        if (is_null($id)) {
            $id = 0;
        }
        $args = array(
            'id' => $id,
            'post_content' => '',
            'post_title' => DateTime::nowTimestamp(),
            'post_excerpt' => 'N/A',
            'post_status' => 'publish',
            'post_type' => BookingPostType::SLUG
        );
        $filtered = \apply_filters('edd_bk_save_booking', $args, $booking);
        $inserted = \wp_insert_post($filtered, true);

        if (\is_wp_error($inserted)) {
            throw new Exception('Failed to insert booking into database.');
        } else {
            $insertedId = intval($inserted);
            $booking->setId($insertedId);
        }
        $this->saveBookingMeta($booking);
        return $insertedId;
    }

    /**
     * Saves a booking instance's data as post meta.
     * 
     * @param Booking $booking The instance whose meta to save.
     */
    public function saveBookingMeta(Booking $booking)
    {
        $meta = array(
            'edd_bk_start' => $booking->getStart()->getTimestamp(),
            'edd_bk_duration' => $booking->getDuration()->getSeconds(),
            'edd_bk_service_id' => $booking->getServiceId(),
            'edd_bk_payment_id' => $booking->getPaymentId(),
            'edd_bk_customer_id' => $booking->getCustomerId()
        );
        $filtered = \apply_filters('edd_bk_save_booking_meta', $meta, $booking);
        foreach ($filtered as $key => $value) {
            \update_post_meta($booking->getId(), $key, $value);
        }
    }
    
    /**
     * Registers the WordPress hooks.
     */
    public function hook()
    {
        $this->getPostType()->hook();
    }

}
