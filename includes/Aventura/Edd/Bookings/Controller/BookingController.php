<?php

namespace Aventura\Edd\Bookings\Controller;

use \Aventura\Diary\DateTime;
use \Aventura\Diary\DateTime\Duration;
use \Aventura\Diary\DateTime\Period;
use \Aventura\Edd\Bookings\CustomPostType\BookingPostType;
use \Aventura\Edd\Bookings\Model\Booking;
use \Exception;

/**
 * Controller class for bookings.
 * 
 * This class is responsible for retrieving stored instance, saving and updating creating instances and querying
 * the storage for instance of bookings.
 */
class BookingController extends ModelCptControllerAbstract
{

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
            // Get the meta data
            $meta = $this->getMeta($id);
            // Add the ID
            $meta['id'] = $id;
            // Create the booking
            $booking = $this->getFactory()->create($meta);
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
    public function query(array $metaQueries = array())
    {
        $args = array(
                'post_type'   => BookingPostType::SLUG,
                'post_status' => 'publish',
                'meta_query'  => $metaQueries
        );
        $filtered = \apply_filters('edd_bk_query_bookings', $args);
        // Submit query and compile array of bookings
        $query = $this->_createQuery($filtered);
        $bookings = array();
        while ($query->have_posts()) {
            $query->the_post();
            $bookings[] = $this->get($query->post->ID);
        }
        // Reset WordPress' query data and return array
        $this->_resetQuery();
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
        // Prepare query args
        $metaQueries = array();
        $serviceIdQuery = array(
                'key'     => 'service_id',
                'value'   => $id
        );
        $serviceIdQuery['compare'] = is_array($id)
                ? 'IN'
                : '=';
        // Add service ID query to the meta query
        $metaQueries[] = $serviceIdQuery;
        // Add date query if period is given
        if ($period !== null) {
            $periodQueries = array(
                    'key'     => 'start',
                    'value'   => array($period->getStart()->getTimestamp(), $period->getEnd()->getTimestamp()),
                    'compare' => 'BETWEEN'
            );
            // Add period query to the meta query
            $metaQueries[] = $periodQueries;
        }
        if (count($metaQueries) > 1) {
            $metaQueries['relation'] = 'AND';
        }
        $filtered = \apply_filters('edd_bk_query_bookings_for_service', $metaQueries, $id);
        return $this->query($filtered);
    }
    
    /**
     * Gets all the bookings for all services using a particular availability.
     * 
     * @param integer $id The availability ID.
     * @return array An array of Booking instances.
     */
    public function getBookingsForAvailability($id)
    {
        $services = $this->getPlugin()->getServiceController()->getServicesForAvailability($id);
        $serviceIds = array_map(function($item) {
            return $item->getId();
        }, $services);
        return empty($serviceIds)
                ? array()
                : $this->getBookingsForService($serviceIds);
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
                'key'     => 'payment_id',
                'value'   => strval($id),
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
                'id'           => $id,
                'post_content' => '',
                'post_title'   => DateTime::nowTimestamp(),
                'post_excerpt' => 'N/A',
                'post_status'  => 'publish',
                'post_type'    => BookingPostType::SLUG
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
                'start'           => $booking->getStart()->getTimestamp(),
                'duration'        => $booking->getDuration()->getSeconds(),
                'service_id'      => $booking->getServiceId(),
                'payment_id'      => $booking->getPaymentId(),
                'customer_id'     => $booking->getCustomerId(),
                'client_timezone' => $booking->getClientTimezone()
        );
        $filtered = \apply_filters('edd_bk_save_booking_meta', $meta, $booking);
        $this->saveMeta($booking->getId(), $filtered);
    }

    /**
     * Registers the WordPress hooks.
     */
    public function hook()
    {
        $this->getPostType()->hook();
    }

    /**
     * Creates bookings using the information from a specific payment.
     * 
     * @param integer $paymentId The payment ID.
     * @return array An array of booking instances.
     */
    public function createFromPayment($paymentId)
    {
        // Get the payment meta
        $payment_meta = \edd_get_payment_meta($paymentId);
        // Get the items that were in the cart for this payment
        $items = $payment_meta['downloads'];
        // Build bookings array
        $bookings = array();
        foreach ($items as $item) {
            // Check if the item ID exists and booking cart info exists
            if (!isset($item['id']) || !isset($item['options']['edd_bk'])) {
                continue;
            }
            // Extract indexes
            $id = $item['id'];
            $info = $item['options']['edd_bk'];
            // Check if the item is a service and has bookings enabled
            $service = $this->getPlugin()->getServiceController()->get($id);
            if (!$service->getBookingsEnabled()) {
                continue;
            }
            $utcTimestamp = intval($info['start']);
            // Check if the service is using day/week as unit, and fix the start timestamp as necessary
            if ($service->isSessionUnit('days', 'weeks')) {
                // UTC timestamp will be correct at 00:00:00, but server time will be offset, making the start/end
                // times fall through to other dates
                $utcTimestamp -= $this->getPlugin()->getServerTimezoneOffsetSeconds();
            }
            $customerId = \edd_get_payment_customer_id($paymentId);
            // Build meta array
            $meta = array(
                    'id'              => 0,
                    'start'           => $utcTimestamp,
                    'duration'        => intval($info['duration']),
                    'client_timezone' => intval($info['timezone']),
                    'service_id'      => intval($service->getId()),
                    'customer_id'     => $customerId,
                    'payment_id'      => $paymentId
            );
            // Add to array
            $bookings[] = $this->getFactory()->create($meta);
        }
        // Return all bookings found
        return $bookings;
    }

    /**
     * {@inheritdoc}
     */
    public function insert(array $data = array(), $wp_error = false)
    {
        $default = array(
                'post_title'   => __('Booking', $this->getPlugin()->getI18n()->getDomain()),
                'post_content' => '',
                'post_type'    => $this->getPostType()->getSlug(),
                'post_status'  => 'publish'
        );
        $args = \wp_parse_args($data, $default);
        $filteredArgs = \apply_filters('edd_bk_new_booking_args', $args);
        $insertedId = parent::insert($filteredArgs, $wp_error);
        return \is_wp_error($insertedId)
                ? null
                : $insertedId;
    }

    /**
     * {@inheritdoc}
     */
    public function saveMeta($id, array $data = array())
    {
        foreach ($data as $key => $value) {
            \update_post_meta($id, $key, $value);
        }
    }

}
