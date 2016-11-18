<?php

namespace Aventura\Edd\Bookings\CustomPostType;

use \Aventura\Diary\DateTime\Duration;
use \Aventura\Edd\Bookings\Controller\AssetsController;
use \Aventura\Edd\Bookings\CustomPostType;
use \Aventura\Edd\Bookings\Model\Booking;
use \Aventura\Edd\Bookings\Plugin;
use \Aventura\Edd\Bookings\Renderer\BookingRenderer;
use \Aventura\Edd\Bookings\Renderer\BookingsCalendarRenderer;
use \Aventura\Edd\Bookings\Renderer\OrdersPageRenderer;
use \Aventura\Edd\Bookings\Renderer\ReceiptRenderer;
use \DateTime;
use \Exception;

/**
 * The Booking custom post type.
 *
 * @author Miguel Muscat <miguelmuscat93@gmail.com>
 */
class BookingPostType extends CustomPostType
{

    /**
     * The CPT slug name.
     */
    const SLUG = 'edd_booking';

    /**
     * Cache used when rendering the CPT table.
     * 
     * Since the rendering in WordPress is done by calling a callback for each table cell, it is pointless to fetch
     * the same booking data multiple times for the cells in a single row. This cache is used to fetch the booking
     * data once when rendering the first cell in a row, and use it for the remaining rows.
     * 
     * @var mixed 
     */
    protected $_tableRowCache;

    /**
     * Constructs a new instance.
     * 
     * @param Plugin $plugin The parent plugin instance.
     */
    public function __construct(Plugin $plugin)
    {
        parent::__construct($plugin, self::SLUG);
        $this->generateLabels(__('Booking', 'eddbk'), __('Bookings', 'eddbk'))
                ->setLabel('all_items', __('Bookings', 'eddbk'))
                ->setHelpLabel()
                ->setDefaultProperties();
    }

    /**
     * Sets the help label, shown in place of the "not found" empty table message.
     *
     * @return BookingPostType This instance.
     */
    public function setHelpLabel()
    {
        $noBookingsText = __('You do not have any bookings!', 'eddbk');
        $addNewUrl = admin_url('post-new.php?post_type=download');
        // Path to "Add New" downloads page
        $addDownloadPageNavigation = sprintf('%1$s %2$s %3$s', __('Downloads', 'eddbk'), '&raquo;', __('Add New', 'eddbk'));
        $addDownloadPageNavigationLink = sprintf('<a href="%1$s">%2$s</a>', $addNewUrl, $addDownloadPageNavigation);
        // Enable bookings option
        $enableBookingsOptionLabel = sprintf('<em>%s</em>', __('Enable Bookings', 'eddbk'));
        // Final help text
        $helpText = sprintf(
            _x(
                'To create a bookable service go to %1$s and tick the %2$s option.',
                '%1$s = "Downloads > Add New". %2$s = "Enable Bookings".',
                'eddbk'
            ),
            $addDownloadPageNavigationLink,
            $enableBookingsOptionLabel
        );
        $this->setLabel('not_found', sprintf('%1$s<br />%2$s', $noBookingsText, $helpText));
        return $this;
    }

    /**
     * Sets the properties to their default.
     * 
     * @return CustomPostType This instance.
     */
    public function setDefaultProperties()
    {
        $properties = array(
            'public'       => false,
            'show_ui'      => true,
            'has_archive'  => false,
            'show_in_menu' => 'edd-bookings',
            'supports'     => false,
            'capabilities' => array(
                'create_posts' => true
            ),
            'map_meta_cap' => true
        );
        $filtered = \apply_filters('edd_bk_booking_cpt_properties', $properties);
        $this->setProperties($filtered);
        return $this;
    }

    /**
     * Registers the metaboxes.
     */
    public function addMetaboxes()
    {
        // Query fix
        global $post, $wp_query;
        $wp_query->post = $post;

        \add_meta_box('edd-bk-booking-details', __('Booking Info', 'eddbk'), array($this, 'renderDetailsMetabox'), $this->getSlug(), 'normal', 'core');
        // \add_meta_box('edd-bk-booking-advanced-times', __('Booking Actions', 'eddbk'), array($this, 'renderActionsMetabox'), $this->getSlug(), 'side', 'core');
    }

    /**
     * Renders the booking details metabox.
     * 
     * @param WP_Post $post The current post.
     */
    public function renderDetailsMetabox($post)
    {
        $booking = (!$post->ID || get_post_status($post->ID) === 'auto-draft')
            ? new Booking(0, \Aventura\Diary\DateTime::now(), Duration::hours(1), 0)
            : $this->getPlugin()->getBookingController()->get($post->ID);
        $data = array(
            'booking' => $booking
        );
        wp_nonce_field('edd_bk_save_meta', 'edd_bk_booking');
        echo $this->getPlugin()->renderView('Admin.Bookings.Edit', $data);
    }

    public function renderActionsMetabox($post)
    {
        printf('<button class="button button-secondary">Cancel</button>');
    }

    /**
     * Called when a booking is saved.
     *
     * @param integer $postId The post ID
     * @param WP_Post $post The post object
     */
    public function onSave($postId, $post)
    {
        if (!$this->_guardOnSave($postId, $post)) {
            return;
        }
        // Check if triggered through a POST request (the WP Admin new/edit page, FES submission, etc.)
        if (filter_input(INPUT_POST, 'start', FILTER_SANITIZE_STRING)) {
            // verify nonce
            \check_admin_referer('edd_bk_save_meta', 'edd_bk_booking');
            // Get the meta from the POST data
            $meta = $this->extractMeta($postId);
            // Save its meta
            $this->getPlugin()->getBookingController()->saveMeta($postId, $meta);
        }
    }

    /**
     * Extracts meta data from a POST request.
     *
     * @param int $postId The ID of the post
     * @return array The extract meta data.
     */
    public function extractMeta($postId)
    {
        $meta = array(
            'id' => $postId
        );
        // Start date and time
        $startStr = filter_input(INPUT_POST, 'start', FILTER_SANITIZE_STRING);
        $startDate = \Aventura\Diary\DateTime::fromString($startStr);
        $start = is_null($startDate)
            ? Datetime::now()
            : eddBookings()->serverTimeToUtcTime($startDate)->getTimestamp();
        $meta['start'] = $start;
        // Calculate duration
        $endStr = filter_input(INPUT_POST, 'end', FILTER_SANITIZE_STRING);
        $endDate = \Aventura\Diary\DateTime::fromString($endStr);
        $end = is_null($endDate)
            ? DateTime::now()
            : eddBookings()->serverTimeToUtcTime($endDate)->getTimestamp();
        $duration = max($start, $end) - min($start, $end) + 1;
        $meta['duration'] = $duration;
        // Service ID
        $serviceId = filter_input(INPUT_POST, 'service_id', FILTER_VALIDATE_INT);
        $service = $this->getPlugin()->getServiceController()->get($serviceId);
        $meta['service_id'] = is_null($service) ? 0 : $serviceId;
        // Customer ID
        $customerId = filter_input(INPUT_POST, 'customer_id', FILTER_VALIDATE_INT);
        $meta['customer_id'] = $customerId;
        // Payment
        $paymentId = filter_input(INPUT_POST, 'payment_id', FILTER_VALIDATE_INT);
        $payment = get_post($paymentId);
        $meta['payment_id'] = is_null($payment) ? 0 : $paymentId;
        // Client timezone
        $meta['client_timezone'] = filter_input(INPUT_POST, 'customer_tz', FILTER_VALIDATE_INT);

        return $meta;
    }

    /**
     * Registers the custom columns for the CPT.
     * 
     * @param array $columns An array of input columns.
     * @return array An array of output columns.
     */
    public function registerCustomColumns($columns)
    {
        return array(
                'cb'          => $columns['cb'],
                'edd-bk-date' => __('Date and Time', 'eddbk'),
                'duration'    => __('Duration', 'eddbk'),
                'customer'    => __('Customer', 'eddbk'),
                'download'    => __('Download', 'eddbk'),
                'payment'     => __('Payment', 'eddbk'),
        );
    }
    
    /**
     * Orders the bookings by their start date.
     * 
     * @param WP_Query $query The WP query
     */
    public function orderBookings($query)
    {
        if (is_admin() && $query->get('post_type') === $this->getSlug() && $query->get('orderby') === '') {
            $query->set('order', 'ASC');
            $query->set('orderby', 'meta_value_num');
            $query->set('meta_key', 'start');
        }
    }

    /**
     * Given a column and a post ID, the function will echo the contents of the
     * respective table cell, for the CPT table.
     * 
     * @param string $column The column slug name.
     * @param string|int $postId The ID of the post.
     */
    public function renderCustomColumns($column, $postId)
    {
        // Stop if post is not a booking post type
        if (get_post_type($postId) === self::SLUG) {
            // Get the booking from cache if the given ID and the cached ID are the same.
            // Otherwise, retrieve from DB and set the cache
            /* @var $booking Booking */
            $booking = null;
            if (!\is_null($this->_tableRowCache) && $this->_tableRowCache->getId() === $postId) {
                $booking = $this->_tableRowCache;
            } else {
                $booking = $this->getPlugin()->getBookingController()->get($postId);
                $this->_tableRowCache = $booking;
            }
            // Generate callback name for cell renderer
            $columnParts = explode('-', $column);
            $ucColumnParts = array_map(function($item) {
                return ucfirst(strtolower($item));
            }, $columnParts);
            $columnCamelCase = implode('', $ucColumnParts);
            $methodName = sprintf('render%sColumn', $columnCamelCase);
            // Check if render method exists
            if (\method_exists($this, $methodName)) {
                // Call it
                $callback = array($this, $methodName);
                $params = array($booking);
                call_user_func_array($callback, $params);
            } else {
                throw new Exception(\sprintf('Column render handler %1$s does not exist in %2$s!', $methodName,
                        \get_called_class()));
            }
        }
    }

    /**
     * Renders the name custom column.
     * 
     * @param Booking $booking The booking instance.
     */
    public function renderCustomerColumn(Booking $booking)
    {
        if ($booking->getCustomerId()) {
            $customer = new \Edd_Customer($booking->getCustomerId());
            $link = \admin_url(
                \sprintf('edit.php?post_type=download&page=edd-customers&view=overview&id=%s', $booking->getCustomerId())
            );
            \printf('<a href="%1$s">%2$s</a>', $link, $customer->name);
        }
    }

    /**
     * Renders the date custom column.
     * 
     * @param Booking $booking The booking instance.
     */
    public function renderEddBkDateColumn(Booking $booking)
    {
        $format = sprintf('%s %s', \get_option('time_format'), \get_option('date_format'));
        $serverTimezoneOffset = intval(\get_option('gmt_offset'));
        $date = $booking->getStart()->copy();
        $text = $date->plus(Duration::hours($serverTimezoneOffset))->format($format);
        printf('<a href="%s">%s</a>', get_edit_post_link($booking->getId()), $text);
    }

    /**
     * Renders the duration custom column.
     * 
     * @param Booking $booking The booking instance.
     */
    public function renderDurationColumn(Booking $booking)
    {
        echo $booking->getDuration();
    }

    /**
     * Renders the download custom column.
     * 
     * @param Booking $booking The booking instance.
     */
    public function renderDownloadColumn(Booking $booking)
    {
        $serviceId = $booking->getServiceId();
        if ($serviceId && get_post($serviceId)) {
            $link = \admin_url(\sprintf('post.php?action=edit&post=%s', $serviceId));
            $text = \get_the_title($serviceId);
            \printf('<a href="%1$s">%2$s</a>', $link, $text);
        }
    }

    /**
     * Renders the payment custom column.
     * 
     * @param Booking $booking The booking instance.
     */
    public function renderPaymentColumn(Booking $booking)
    {
        $paymentId = $booking->getPaymentId();
        if ($paymentId && get_post($paymentId)) {
            $link = \admin_url(
                \sprintf('edit.php?post_type=download&page=edd-payment-history&view=view-order-details&id=%s', $paymentId)
            );
            $text = sprintf(__('View Order Details', 'edd'), $paymentId);
            \printf('<a href="%1$s">%2$s</a>', $link, $text);
        }
    }

    /**
     * Filters the row actions for the Bookings CPT.
     *
     * @param array $actions The row actions to filter.
     * @param \WP_Post $post The post for which the row actions will be filtered.
     * @return array The filtered row actions.
     */
    public function filterRowActions($actions, $post)
    {
        // If post type is our bookings cpt
        if ($post->post_type === $this->getSlug()) {
            // Remove the quickedit
            unset($actions['inline hide-if-no-js']);
        }
        return $actions;
    }
    
    /**
     * Filters the bulk actions for the Booking CPT.
     * 
     * @param array $actions The bulk actions to filter.
     * @return array The filtered bulk actions.
     */
    public function filterBulkActions($actions)
    {
        unset($actions['edit']);
        return $actions;
    }

    /**
     * Disables autosave for this CPT.
     * 
     * Autosave exists as a front-end script.
     */
    public function disableAutosave()
    {
        if (\get_post_type() === self::SLUG) {
            \wp_dequeue_script('autosave');
        }
    }

    /**
     * Callback function for completed purchases. Creates the booking form the purchase
     * and saves it in the DB.
     *
     * @uses hook::action::edd_update_payment_status
     * @param string|int $paymentId The ID of the payment.
     * @param string $status The new payment status.
     * @param string $prevStatus The previous payment status.
     */
    public function createFromPayment($paymentId, $status, $prevStatus)
    {
        if ($prevStatus === 'publish' || $prevStatus === 'complete') {
            return; // Make sure that payments are only completed once
	}
	// Make sure the payment completion is only processed when new status is complete
	if ($status !== 'publish' && $status !== 'complete') {
            return;
	}
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
     * Renders the bookings info in the EDD receipt page.
     * 
     * @param EDD_Payment $payment The payment for the receipt.
     * @param array $receiptArgs Optional receipt argumented.
     */
    public function renderBookingsInfoReceipt($payment, $receiptArgs)
    {
        $renderer = new ReceiptRenderer($payment);
        echo $renderer->render();
    }

    /**
     * Renders the booking info on the Orders page.
     * 
     * @param integer|WP_Post $payment The Id of the payment or the WP_Post object for the payment.
     * @param array $args Optional array of arguments to pass to the renderer.
     */
    public function renderBookingInfoOrdersPage($payment, $args = array())
    {
        $paymentId = is_object($payment)
            ? $payment->ID
            : $payment;
        // Get the cart details for this payment
        $cartItems = edd_get_payment_meta_cart_details($paymentId);
        // Stop if not an array
        if (!is_array($cartItems)) {
            return;
        }
        // Get the bookings for this payment
        $bookings = $this->getPlugin()->getBookingController()->getBookingsForPayment($paymentId);
        if ($bookings === NULL || count($bookings) === 0) {
            return;
        }
        $renderer = new OrdersPageRenderer($bookings);
        echo $renderer->render($args);
    }
    
    /**
     * Renders the Calendar View button in the CPT table page.
     * 
     * @global string $typenow Current post type
     * @param string $which Context: 'top' or 'bottom' of the CPT table
     */
    public function renderCalendarButton($which)
    {
        global $typenow;
        if ($typenow === $this->getSlug() && $which === 'top') {
            $buttonText = __('Calendar View', 'eddbk');
            $icon = '<i class="fa fa-calendar"></i>';
            $url = admin_url('admin.php?page=edd-bk-calendar');
            //$button = sprintf('<a href="%s" class="button button-primary">%s %s</a>', $url, $icon, $buttonText);
            //printf('<div class="alignleft actions edd-bk-admin-calendar-button">%s</div>', $button);
            printf('<a href="%s" class="page-title-action edd-bk-calendar-view-link">%s %s</a>', $url, $icon, $buttonText);
        }
    }

    /**
     * Registers the Calendar menu.
     */
    public function registerMenu()
    {
        $parent = $this->getPlugin()->getMenuSlug();
        $slug = 'edd-bk-calendar';
        $title = __('Calendar', 'eddbk');
        add_submenu_page($parent, $title, $title, 'manage_shop_settings', $slug, array($this, 'renderCalendarPage'));
    }
    
    /**
     * Renders the Calendar page.
     */
    public function renderCalendarPage()
    {
        $renderer = new BookingsCalendarRenderer($this->getPlugin());
        echo $renderer->render();
    }
    
    /**
     * AJAX handler for client request to fetch all bookings for a set of services and a time range.
     */
    public function getAjaxBookingsForCalendar()
    {
        \check_admin_referer('edd_bk_calendar_ajax', 'edd_bk_calendar_ajax_nonce');
        $fes = filter_input(INPUT_POST, 'fes', FILTER_VALIDATE_BOOLEAN);
        if (!\current_user_can('manage_options') && !$fes) {
            die;
        }
        $services = filter_input(INPUT_POST, 'services', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
        if ($fes) {
            $bookings = $this->getPlugin()->getIntegration('fes')->getBookingsForUser();
        } else {
            $bookings = (is_array($services) && count($services) > 0 && !in_array('0', $services))
                    ? $this->getPlugin()->getBookingController()->getBookingsForService($services)
                    : $this->getPlugin()->getBookingController()->query();
        }
        $response = array();
        foreach ($bookings as $booking) {
            /* @var $booking Booking */
            $serviceTitle = ($booking->getServiceId())
                ? \get_the_title($booking->getServiceId())
                : __('No service', 'eddbk');
            $response[] = array(
                    'bookingId' => $booking->getId(),
                    'title'     => $serviceTitle,
                    'start'     => $this->getPlugin()->utcTimeToServerTime($booking->getStart())->format(DateTime::ISO8601),
                    'end'       => $this->getPlugin()->utcTimeToServerTime($booking->getEnd())->format(DateTime::ISO8601)
            );
        }
        echo json_encode($response);
        die;
    }
    
    /**
     * AJAX handler for rendering the booking info pane. This is used by the calendar when a booking is clicked.
     */
    public function getAjaxBookingInfo()
    {
        \check_admin_referer('edd_bk_calendar_ajax', 'edd_bk_calendar_ajax_nonce');
        $referer = wp_get_referer();
        if (!$referer) {
            die;
        }
        $bookingId = filter_input(INPUT_POST, 'bookingId', FILTER_VALIDATE_INT);
        $response = array();
        if (!$bookingId) {
            $response['error'] = 'Invalid booking ID given.';
        } else {
            $booking = $this->getPlugin()->getBookingController()->get($bookingId);
            $renderer = new BookingRenderer($booking);
            $args = array(
                'table_class'       => 'fixed',
                'advanced_times'    => false
            );
            if (filter_input(INPUT_POST, 'fesLinks', FILTER_VALIDATE_BOOLEAN)) {
                $args['service_link'] = add_query_arg(array('task' => 'edit-product', 'post_id' => '%s'), $referer);
                $args['view_details_link'] = add_query_arg(array('task' => 'edit-booking', 'booking_id' => '%s'), $referer);
                $args['payment_link'] = EDD_FES()->vendors->vendor_can_view_orders()
                    ? add_query_arg(array('task' => 'edit-order', 'order_id' => '%s'), $referer)
                    : null;
                $args['customer_link'] = null;
            }
            $response['output'] = $renderer->render($args);
        }
        echo json_encode($response);
        die;
    }

    /**
     * Registers the WordPress hooks.
     */
    public function hook()
    {
        $this->getPlugin()->getHookManager()
            // Register CPT
            ->addAction('init', $this, 'register', 10)
            // Hook for registering metabox
            ->addAction('add_meta_boxes', $this, 'addMetaboxes')
            // Hook for saving bookings
            ->addAction('save_post', $this, 'onSave', 10, 2)
            // Hooks for custom columns
            ->addAction('manage_edd_booking_posts_columns', $this, 'registerCustomColumns')
            ->addAction('manage_posts_custom_column', $this, 'renderCustomColumns', 10, 2)
            // Hooks for row actions
            ->addFilter('post_row_actions', $this, 'filterRowActions', 10, 2)
            // Disable autosave by dequeueing the autosave script for this cpt
            ->addAction('admin_enqueue_scripts', $this, 'disableAutosave')
            // Hook to create bookings on purchase completion
            ->addAction('edd_update_payment_status', $this, 'createFromPayment', 8, 3)
            // Hook to show bookings in receipt
            ->addAction('edd_payment_receipt_after_table', $this, 'renderBookingsInfoReceipt', 10, 2)
            // Show booking info on Orders page
            ->addAction('edd_view_order_details_files_after', $this, 'renderBookingInfoOrdersPage')
            // AJAX handlers
            ->addAction('wp_ajax_edd_bk_get_bookings_for_calendar', $this, 'getAjaxBookingsForCalendar')
            ->addAction('wp_ajax_edd_bk_get_bookings_info', $this, 'getAjaxBookingInfo')
            // Hooks for removing bulk actions
            ->addFilter(sprintf('bulk_actions-edit-%s', $this->getSlug()), $this, 'filterBulkActions')
            // Show calendar button in table page
            ->addAction('manage_posts_extra_tablenav', $this, 'renderCalendarButton')
            // Registers menu items
            ->addAction('admin_menu', $this, 'registerMenu')
            // Filter updated notice message
            ->addFilter('post_updated_messages', $this, 'filterUpdatedMessages')
            // Order bookings in list table
            ->addAction('pre_get_posts', $this, 'orderBookings');
    }

}
