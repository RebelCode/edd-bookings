<?php

namespace RebelCode\EddBookings;

use \Dhii\App\AppInterface;
use \Psr\EventManager\EventManagerInterface;
use \RebelCode\EddBookings\System\Component\AbstractBaseComponent;

/**
 * Description of BookingPaymentBridge
 *
 * @since [*next-version*]
 */
class BookingPaymentBridge extends AbstractBaseComponent
{

    /**
     * The event manager.
     *
     * @since [*next-version*]
     *
     * @var EventManagerInterface
     */
    protected $eventManager;

    /**
     * Constructor.
     *
     * @since [*next-version*]
     *
     * @param AppInterface $app The parent app instance.
     * @param EventManagerInterface $eventManager The event manager.
     */
    public function __construct(
        AppInterface $app,
        EventManagerInterface $eventManager
    ) {
        parent::__construct($app);

        $this->setEventManager($eventManager);
    }

    /**
     * {@inheritdoc}
     *
     * @since [*next-version*]
     */
    public function onAppReady()
    {
        $this->getEventManager()
            ->attach('edd_update_payment_status', $this->_callback('createBookingFromPayment'));
    }

    public function createBookingFromPayment($paymentId, $status, $prevStatus)
    {
        // Stop if the payment was already previously completed
        if ($prevStatus === 'publish' || $prevStatus === 'complete') {
            return;
        }

        // Stop if payment has not been completed
        if ($status !== 'publish' && $status !== 'complete') {
            return;
        }

        // Get the items that were in the cart when this payment was made
        $paymentMeta = \edd_get_payment_meta($paymentId);
        $items = $paymentMeta['downloads'];

        foreach ($items as $item) {
            $this->createBookingForCartItem($item, $paymentId);
        }

        return;

        $controller = $this->getPlugin()->getBookingController();
        $bookings = $controller->createFromPayment($paymentId);
        foreach ($bookings as $booking) {
            /* @var $booking Booking */
            $service = $this->getPlugin()->getServiceController()->get($booking->getServiceId());
            if ($service->canBook($booking)) {
                $insertedId = $controller->insert();
                $booking->setId($insertedId);
                $controller->saveBookingMeta($booking);
            }
        }
    }

    /**
     *
     * @TODO Finish after services
     *
     * @param type $item
     * @param type $paymentId
     * @return type
     */
    public function createBookingForCartItem($item, $paymentId)
    {
        // Check if the item ID exists and booking cart info exists
        if (!isset($item['id']) || !isset($item['options']['edd_bk'])) {
            return;
        }

        // Extract indexes
        $id = $item['id'];
        $info = $item['options']['edd_bk'];

        // Check if the item is a service and has bookings enabled
        $service = $this->getPlugin()->getServiceController()->get($id);
        if (!$service->getBookingsEnabled()) {
            return;
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

        return $meta; // ?
    }

    /**
     * Gets the event manager.
     *
     * @since [*next-version*]
     *
     * @return EventManagerInterface The event manager instance.
     */
    public function getEventManager()
    {
        return $this->eventManager;
    }

    /**
     * Sets the event manager.
     *
     * @since [*next-version*]
     *
     * @param EventManagerInterface $eventManager The event manager instance.
     *
     * @return $this This instance.
     */
    public function setEventManager($eventManager)
    {
        $this->eventManager = $eventManager;

        return $this;
    }
}
