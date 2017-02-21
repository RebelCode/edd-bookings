<?php

namespace RebelCode\EddBookings\CustomPostType\Booking;

use \Dhii\App\AppInterface;
use \Psr\EventManager\EventManagerInterface;
use \RebelCode\EddBookings\Block\Html\LinkTag;
use \RebelCode\EddBookings\Controller\BookingController;
use \RebelCode\EddBookings\CustomPostType;
use \RebelCode\EddBookings\Model\Booking;
use \RebelCode\EddBookings\System\Component\AbstractBaseComponent;
use \RebelCode\EddBookings\Utils\DateTimeFormatterInterface;

/**
 * Component for the bookings posts table.
 *
 * @since [*next-version*]
 */
class PostsTable extends AbstractBaseComponent
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
     * The booking controller.
     *
     * @since [*next-version*]
     *
     * @var BookingController
     */
    protected $bookingController;

    /**
     * The custom post type.
     *
     * @since [*next-version*]
     *
     * @var string
     */
    protected $cpt;

    /**
     * The date time formatter.
     *
     * @since [*next-version*]
     *
     * @var DateTimeFormatterInterface
     */
    protected $dateTimeFormatter;

    /**
     * Constructor.
     *
     * @since [*next-version*]
     *
     * @param AppInterface $app The parent app.
     * @param EventManagerInterface $eventManager The event manager.
     * @param BookingController $bookingController The booking controller.
     * @param CustomPostType $cpt The custom post type.
     * @param DateTimeFormatterInterface $dateTimeFormatter The datetime formatter.
     */
    public function __construct(
        AppInterface $app,
        EventManagerInterface $eventManager,
        BookingController $bookingController,
        CustomPostType $cpt,
        DateTimeFormatterInterface $dateTimeFormatter
    ) {
        parent::__construct($app);

        $this->setEventManager($eventManager)
            ->setBookingController($bookingController)
            ->setCpt($cpt)
            ->setDateTimeFormatter($dateTimeFormatter);
    }

    /**
     * {@inheritdoc}
     *
     * @since [*next-version*]
     */
    public function onAppReady()
    {
        $this->getEventManager()->attach(
            $this->_slugHook('manage_%s_posts_columns'),
            $this->_callback('filterColumns')
        );

        $this->getEventManager()->attach(
            'manage_posts_custom_column',
            $this->_callback('renderCustomColumns')
        );

        $this->getEventManager()->attach(
            'post_row_actions',
            $this->_callback('filterRowActions')
        );

        $this->getEventManager()->attach(
            sprintf('bulk_actions-edit-%s', $this->getCpt()->getSlug()),
            $this->_callback('filterBulkActions')
        );

        $this->getEventManager()->attach(
           'pre_get_posts',
            $this->_callback('orderBookings')
        );
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

    /**
     * Gets the booking controller.
     *
     * @since [*next-version*]
     *
     * @return BookingController The booking controller.
     */
    public function getBookingController()
    {
        return $this->bookingController;
    }

    /**
     * Sets the booking controller.
     *
     * @since [*next-version*]
     *
     * @param BookingController $bookingController The booking controller instance.
     *
     * @return $this This instance.
     */
    public function setBookingController(BookingController $bookingController)
    {
        $this->bookingController = $bookingController;
        return $this;
    }

    /**
     * Gets the custom post type.
     *
     * @since [*next-version*]
     *
     * @return CustomPostType The custom post type.
     */
    public function getCpt()
    {
        return $this->cpt;
    }

    /**
     * Sets the custom post type
     *
     * @since [*next-version*]
     *
     * @param CustomPostType $cpt The new custom post type instance.
     *
     * @return $this This instance.
     */
    public function setCpt(CustomPostType $cpt)
    {
        $this->cpt = $cpt;

        return $this;
    }

    /**
     * Gets the datetime formatter.
     *
     * @since [*next-version*]
     *
     * @return DateTimeFormatterInterface The datetime formatter instance.
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
     * @param DateTimeFormatterInterface $dateTimeFormatter The datetime formatter instance.
     *
     * @return $this This instance.
     */
    public function setDateTimeFormatter(DateTimeFormatterInterface $dateTimeFormatter)
    {
        $this->dateTimeFormatter = $dateTimeFormatter;

        return $this;
    }

    /**
     * Filters the default table columns.
     *
     * @since [*next-version*]
     *
     * @param array $columns The input columns.
     *
     * @return array The output columns.
     */
    public function filterColumns($columns)
    {
        $preserved  = array( 'cb' => $columns['cb'] );
        $newColumns = $this->getColumns();
        $output     = array_merge($preserved, $newColumns);

        return $output;
    }

    /**
     * Filters the row actions for the Bookings CPT.
     *
     * @since [*next-version*]
     *
     * @param array $actions The row actions to filter.
     * @param \WP_Post $post The post for which the row actions will be filtered.
     *
     * @return array The filtered row actions.
     */
    public function filterRowActions($actions, $post)
    {
        // If post type is our bookings cpt
        if ($post->post_type === $this->getCpt()->getSlug()) {
            // Remove the quickedit
            unset($actions['inline hide-if-no-js']);
        }

        return $actions;
    }

    /**
     * Filters the bulk actions for the Booking CPT.
     *
     * @since [*next-version*]
     *
     * @param array $actions The bulk actions to filter.
     *
     * @return array The filtered bulk actions.
     */
    public function filterBulkActions($actions)
    {
        unset($actions['edit']);

        return $actions;
    }

    /**
     * Orders the bookings by their start date.
     *
     * @since [*next-version*]
     *
     * @param \WP_Query $query The WP query
     */
    public function orderBookings($query)
    {
        if (is_admin() && $query->get('post_type') === $this->getCpt()->getSlug() && $query->get('orderby') === '') {
            $query->set('order', 'ASC');
            $query->set('orderby', 'meta_value_num');
            $query->set('meta_key', 'start');
        }
    }

    /**
     * Gets the table columns.
     *
     * @since [*next-version*]
     *
     * @return type
     */
    public function getColumns()
    {
        return array(
            'edd-bk-date' => __('Date and Time', 'eddbk'),
            'duration'    => __('Duration', 'eddbk'),
            'customer'    => __('Customer', 'eddbk'),
            'download'    => __('Download', 'eddbk'),
            'payment'     => __('Payment', 'eddbk'),
        );
    }

    /**
     * Renders a column cell.
     *
     * @since [*next-version*]
     *
     * @param string $column The current column ID.
     * @param int $postId The current post ID.
     */
    public function renderCustomColumns($column, $postId)
    {
        // Stop if post is not a booking post type
        if (get_post_type($postId) !== $this->getCpt()->getSlug()) {
            return;
        }

        $booking = $this->getBookingController()->get($postId);

        switch ($column) {
            case 'edd-bk-date':
                echo $this->renderDateColumn($booking);
                break;
            case 'duration':
                echo $this->renderDurationColumn($booking);
                break;
            case 'customer':
                echo $this->renderCustomerColumn($booking);
                break;
            case 'download':
                echo $this->renderDownloadColumn($booking);
                break;
            case 'payment':
                echo $this->renderPaymentColumn($booking);
                break;
        }
    }

    /**
     * Renders the date column.
     *
     * @since [*next-version*]
     *
     * @param Booking $booking The booking instance.
     *
     * @return string The rendered output.
     */
    public function renderDateColumn(Booking $booking)
    {
        return $this->getDateTimeFormatter()->formatDatetime($booking->getStart());
    }

    /**
     * Renders the duration column.
     *
     * @since [*next-version*]
     *
     * @param Booking $booking The booking instance.
     *
     * @return string The rendered output.
     */
    public function renderDurationColumn(Booking $booking)
    {
        return $this->getDateTimeFormatter()->formatDuration($booking->getDuration());
    }

    /**
     * Renders the customer column.
     *
     * @since [*next-version*]
     *
     * @param Booking $booking The booking instance.
     *
     * @return string The rendered output.
     */
    public function renderCustomerColumn(Booking $booking)
    {
        $customerId = (int) $booking->getCustomerId();
        $customer   = new \EDD_Customer($customerId);

        if (is_null($customer->name) && is_null($customer->email)) {
            return __('No Customer', 'eddbk');
        }

        return new LinkTag($customer->name, $this->_customerUrl($customerId));
    }

    /**
     * Renders the download column.
     *
     * @since [*next-version*]
     *
     * @param Booking $booking The booking instance.
     *
     * @return string The rendered output.
     */
    public function renderDownloadColumn(Booking $booking)
    {
        $serviceId = (int) $booking->getServiceId();
        $service   = \get_post($serviceId);

        if ($serviceId === 0 || is_null($service)) {
            return __('No Service', 'eddbk');
        }

        return new LinkTag($service->post_title, $this->_serviceUrl($serviceId));
    }

    /**
     * Renders the payment column.
     *
     * @since [*next-version*]
     *
     * @param Booking $booking The booking instance.
     *
     * @return string The rendered output.
     */
    public function renderPaymentColumn(Booking $booking)
    {
        $paymentId = (int) $booking->getPaymentId();
        $payment   = \get_post($paymentId);

        if ($paymentId === 0 || is_null($payment)) {
            return __('No Payment', 'eddbk');
        }

        return new LinkTag(sprintf('Order #%d', $paymentId), $this->_paymentUrl($paymentId));
    }

    /**
     * Formats a WordPress hook handle with the CPT slug.
     *
     * @since [*next-version*]
     *
     * @param string $hookPattern The hook pattern, similar to a `printf` pattern.
     *
     * @return string The formatted hook handle.
     */
    protected function _slugHook($hookPattern)
    {
        return sprintf($hookPattern, $this->getCpt()->getSlug());
    }

    /**
     *
     */
    protected function _customerUrl($id)
    {
        return \admin_url(
            sprintf('edit.php?post_type=download&page=edd-customers&view=overview&id=%s', $id)
        );
    }

    /**
     *
     */
    protected function _serviceUrl($id)
    {
        return \admin_url(
            sprintf('post.php?action=edit&post=%s', $id)
        );
    }

    /**
     *
     */
    protected function _paymentUrl($id)
    {
        return admin_url(
            sprintf('edit.php?post_type=download&page=edd-payment-history&view=view-order-details&id=%s', $id)
        );
    }
}
