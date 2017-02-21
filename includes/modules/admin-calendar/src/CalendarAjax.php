<?php

namespace RebelCode\EddBookings\Admin\Calendar;

use \Dhii\App\AppInterface;
use \RebelCode\Diary\DateTime\DateTime;
use \RebelCode\EddBookings\Admin\Calendar\Block\BookingInfoBlock;
use \RebelCode\EddBookings\AjaxManager;
use \RebelCode\EddBookings\Controller\BookingController;
use \RebelCode\EddBookings\Model\Booking;
use \RebelCode\EddBookings\System\Component\AbstractBaseComponent;
use \RebelCode\EddBookings\Utils\DateTimeFormatter;
use \RebelCode\EddBookings\Utils\DateTimeFormatterInterface;
/**
 * AJAX handler for the admin calendar.
 *
 * @since [*next-version*]
 */
class CalendarAjax extends AbstractBaseComponent
{
    /**
     * Gets the ajax manager.
     *
     * @since [*next-version*]
     *
     * @var AjaxManager
     */
    protected $ajaxManager;

    /**
     * The booking controller.
     *
     * @since [*next-version*]
     *
     * @var BookingController
     */
    protected $bookingController;

    /**
     * The datetime formatter.
     *
     * @since [*next-version*]
     *
     * @var DateTimeFormatterInterface
     */
    protected $dateTimeFormatter;

    /**
     * {@inheritdoc}
     *
     * @since [*next-version*]
     *
     * @param AjaxManager $ajaxManager The AJAX manager instance.
     */
    public function __construct(
        AppInterface $app,
        AjaxManager $ajaxManager,
        BookingController $bookingController,
        DateTimeFormatterInterface $dateTimeFormatter
    ) {
        parent::__construct($app);

        $this->setAjaxManager($ajaxManager)
            ->setBookingController($bookingController)
            ->setDateTimeFormatter($dateTimeFormatter);
    }

    /**
     * {@inheritdoc}
     *
     * @since [*next-version*]
     */
    public function onAppReady()
    {
        $this->getAjaxManager()
            ->addHandler('calendar_get_bookings', $this->_callback('ajaxCalendarBookings'))
            ->addHandler('calendar_get_booking_info', $this->_callback('ajaxBookingInfo'));
    }

    /**
     *
     * @param type $event
     */
    public function ajaxCalendarBookings($request, &$response, $args)
    {
        \check_admin_referer('edd_bk_calendar_ajax', 'edd_bk_calendar_ajax_nonce');

        if (!\current_user_can('manage_options')) {
            return;
        }

        $services = filter_input(INPUT_POST, 'services', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);

        $servicesQuery = array(
            'meta_query' => array(
                'key'     => 'service_id',
                'value'   => $services,
                'compate' => 'IN'
            )
        );

        $bookings = (!empty($services) && is_array($services) && !in_array('0', $services))
            ? $this->getBookingController()->query($servicesQuery)
            : $this->getBookingController()->query(array());

        $this->_prepareResponse($response);

        // Pre-fetch to reduce calls during iteration
        $formatter = $this->getDateTimeFormatter();
        
        foreach ($bookings as $booking) {
            /* @var $booking Booking */
            $serviceTitle = ($booking->getServiceId())
                ? \get_the_title($booking->getServiceId())
                : __('No service', 'eddbk');

            $start = $booking->getStart();
            $end   = $booking->getEnd();

            $response[] = array(
                'bookingId' => $booking->getId(),
                'title'     => $serviceTitle,
                'start'     => $formatter->format(\DateTime::ISO8601, $start),
                'end'       => $formatter->format(\DateTime::ISO8601, $end)
            );
        }
    }

    /**
     *
     * @param type $event
     */
    public function ajaxBookingInfo($request, &$response, $args)
    {
        \check_admin_referer('edd_bk_calendar_ajax', 'edd_bk_calendar_ajax_nonce');

        $referer = wp_get_referer();
        if (!$referer) {
            die;
        }

        $bookingIdArg = isset($args['bookingId'])
            ? $args['bookingId']
            : null;
        $bookingId    = filter_var($bookingIdArg, FILTER_VALIDATE_INT);

        if (!$bookingId) {
            $response['error'] = sprintf('Invalid booking ID given: "%s"', $bookingId);
            return;
        }

        $booking            = $this->getBookingController()->get($bookingId);
        $block              = new BookingInfoBlock($booking, $this->getDateTimeFormatter());
        $response['result'] = (string) $block;
    }

    /**
     * FullCalendar requires a special kind of response: an array of event objects.
     * Our standard AJAX responses will not be recognised.
     *
     * @since [*next-version*]
     *
     * @param array $response The response
     */
    protected function _prepareResponse(&$response)
    {
        unset($response['result']);
        unset($response['success']);
        unset($response['error']);
    }

    /**
     * Gets the AJAX manager.
     *
     * @since [*next-version*]
     *
     * @return AjaxManager The AJAX manager instance.
     */
    public function getAjaxManager()
    {
        return $this->ajaxManager;
    }

    /**
     * Sets the AJAX manager.
     *
     * @since [*next-version*]
     *
     * @param AjaxManager $ajaxManager The AJAX manager instance.
     *
     * @return $this This instance.
     */
    public function setAjaxManager(AjaxManager $ajaxManager)
    {
        $this->ajaxManager = $ajaxManager;

        return $this;
    }

    public function getBookingController()
    {
        return $this->bookingController;
    }

    public function setBookingController(BookingController $bookingController)
    {
        $this->bookingController = $bookingController;
        return $this;
    }

    /**
     * Gets the datetime formatter.
     *
     * @since [*next-version*]
     *
     * @return DateTimeFormatter The datetime formatter instance.
     */
    public function getDateTimeFormatter()
    {
        return $this->dateTimeFormatter;
    }

    /**
     * Sets the datetime formatter.
     *
     * @since [*next-version*]
     *
     * @param DateTimeFormatterInterface $dateTimeFormatter The datetime formatter.
     *
     * @return $this This instance.
     */
    public function setDateTimeFormatter(DateTimeFormatterInterface $dateTimeFormatter)
    {
        $this->dateTimeFormatter = $dateTimeFormatter;

        return $this;
    }
}
