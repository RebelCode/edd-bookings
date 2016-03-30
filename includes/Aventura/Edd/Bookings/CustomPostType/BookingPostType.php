<?php

namespace Aventura\Edd\Bookings\CustomPostType;

use \Aventura\Diary\DateTime\Duration;
use \Aventura\Edd\Bookings\CustomPostType;
use \Aventura\Edd\Bookings\Model\Booking;
use \Aventura\Edd\Bookings\Plugin;
use \Aventura\Edd\Bookings\Renderer\BookingRenderer;
use \Aventura\Edd\Bookings\Renderer\BookingsCalendarRenderer;
use \Aventura\Edd\Bookings\Renderer\OrdersPageRenderer;
use \Aventura\Edd\Bookings\Renderer\ReceiptRenderer;
use \Exception;

define('EDD_BK_CALENDAR_ENABLED', true);

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
        $this->generateLabels('Booking', 'Bookings')
                ->setDefaultProperties();
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
                'supports'     => false
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
        $textDomain = $this->getPlugin()->getI18n()->getDomain();
        \add_meta_box('edd-bk-booking-details', __('Booking Details', $textDomain),
                array($this, 'renderDetailsMetabox'), $this->getSlug(), 'normal', 'core');
        \remove_meta_box('submitdiv', $this->getSlug(), 'normal');
    }
    
    /**
     * Renders the booking details metabox.
     * 
     * @param WP_Post $post The current post.
     */
    public function renderDetailsMetabox($post)
    {
        $booking = (empty($post->ID))
                ? $this->getPlugin()->getBookingController()->getFactory()->create(array('id' => 0))
                : $this->getPlugin()->getBookingController()->get($post->ID);
        $renderer = new BookingRenderer($booking);
        echo $renderer->render();
    }

    /**
     * Registers the custom columns for the CPT.
     * 
     * @param array $columns An array of input columns.
     * @return array An array of output columns.
     */
    public function registerCustomColumns($columns)
    {
        $textDomain = $this->getPlugin()->getI18n()->getDomain();
        return array(
                'cb'           => $columns['cb'],
                'edd-bk-date'  => __('Date and Time', $textDomain),
                'duration'     => __('Duration', $textDomain),
                'customer'     => __('Customer', $textDomain),
                'download'     => __('Download', $textDomain),
                'availability' => __('Schedule', $textDomain),
                'payment'      => __('Payment', $textDomain),
        );
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
        $customer = new \Edd_Customer($booking->getCustomerId());
        $link = \admin_url(
                \sprintf(
                        'edit.php?post_type=download&page=edd-customers&view=overview&id=%s', $booking->getCustomerId()
                )
        );
        \printf('<a href="%1$s">%2$s</a>', $link, $customer->name);
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
        echo $date->plus(Duration::hours($serverTimezoneOffset))->format($format);
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
        $link = \admin_url(\sprintf('post.php?action=edit&post=%s', $serviceId));
        $text = \get_the_title($serviceId);
        \printf('<a href="%1$s">%2$s</a>', $link, $text);
    }
    
    /**
     * Renders the availability custom column.
     * 
     * @param Booking $booking The booking instance.
     */
    public function renderAvailabilityColumn(Booking $booking)
    {
        $serviceId = $booking->getServiceId();
        $availabilityId = $this->getPlugin()->getServiceController()->get($serviceId)->getAvailability()->getId();
        if (is_null($availabilityId) || $availabilityId === 0) {
            echo 'None';
        } else {
            $link = \admin_url(\sprintf('post.php?action=edit&post=%s', $availabilityId));
            $text = \get_the_title($availabilityId);
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
        $link = \admin_url(
                \sprintf(
                        'edit.php?post_type=download&page=edd-payment-history&view=view-order-details&id=%s', $paymentId
                )
        );
        $text = sprintf(__('View Order Details', 'edd'), $paymentId);
        \printf('<a href="%1$s">%2$s</a>', $link, $text);
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
        if ($post->post_type === self::SLUG) {
            // Remove the quickedit
            unset($actions['inline hide-if-no-js']);
        }
        return $actions;
    }

    /**
     * Sets the screen layout (number of columns) for the Bookings Edit page.
     * 
     * @return integer The number of columns.
     */
    public function setScreenLayout()
    {
        return 1;
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
     * @uses hook::action::edd_complete_purchase
     * @param string|int $paymentId The ID of the payment.
     */
    public function createFromPayment($paymentId)
    {
        $controller = $this->getPlugin()->getBookingController();
        $bookings = $controller->createFromPayment($paymentId);
        foreach ($bookings as $booking) {
            /* @var $booking Booking */
            $insertedId = $controller->insert();
            $booking->setId($insertedId);
            $controller->saveBookingMeta($booking);
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
     * @TODO
     * @param integer $paymentId The Id of the payment.
     */
    public function renderBookingInfoOrdersPage($paymentId)
    {
        // Get the cart details for this payment
        $cartItems = edd_get_payment_meta_cart_details($paymentId);
        // Stop if not an array
        if (!is_array($cartItems)) {
            return;
        }
        // Get the bookings for this payment
        $bookings = $this->getPlugin()->getBookingController()->getBookingsForPayemnt($paymentId);
        if ($bookings === NULL || count($bookings) === 0) {
            return;
        }
        $renderer = new OrdersPageRenderer($bookings);
        echo $renderer->render();
    }
    
    public function renderCalendarButton($which)
    {
        global $typenow;
        if ($typenow === $this->getSlug() && $which === 'top') {
            $buttonText = __('Calendar View', $this->getPlugin()->getI18n()->getDomain());
            $icon = '<i class="fa fa-calendar"></i>';
            $url = admin_url('admin.php?page=edd-bk-calendar');
            //$button = sprintf('<a href="%s" class="button button-primary">%s %s</a>', $url, $icon, $buttonText);
            //printf('<div class="alignleft actions edd-bk-admin-calendar-button">%s</div>', $button);
            printf('<a href="%s" class="page-title-action edd-bk-calendar-view-link">%s %s</a>', $url, $icon, $buttonText);
        }
    }

    public function registerMenu()
    {
        $parent = $this->getPlugin()->getMenuSlug();
        $slug = 'edd-bk-calendar';
        $title = __('Calendar', $this->getPlugin()->getI18n()->getDomain());
        add_submenu_page($parent, $title, $title, 'manage_shop_settings', $slug, array($this, 'renderCalendarPage'));
    }
    
    public function renderCalendarPage()
    {
        $renderer = new BookingsCalendarRenderer($this->getPlugin());
        echo $renderer->render();
    }
    
    public function getAjaxBookingsForCalendar()
    {
        $schedules = filter_input(INPUT_POST, 'schedules', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
        $bookings = (is_array($schedules) && !empty($schedules) && !in_array('0', $schedules))
                ? $this->getPlugin()->getBookingController()->getBookingsForAvailability($schedules)
                : $this->getPlugin()->getBookingController()->query();
        $response = array();
        foreach ($bookings as $booking) {
            /* @var $booking Booking */
            $response[] = array(
                    'bookingId' => $booking->getId(),
                    'title'     => \get_the_title($booking->getServiceId()),
                    'start'     => $this->getPlugin()->utcTimeToServerTime($booking->getStart())->format(\DateTime::ISO8601),
                    'end'       => $this->getPlugin()->utcTimeToServerTime($booking->getEnd())->format(\DateTime::ISO8601)
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
        $bookingId = filter_input(INPUT_POST, 'bookingId', FILTER_VALIDATE_INT);
        $response = array();
        if (!$bookingId) {
            $response['error'] = 'Invalid booking ID given.';
        } else {
            $booking = $this->getPlugin()->getBookingController()->get($bookingId);
            $renderer = new BookingRenderer($booking);
            $response['output'] = $renderer->render(array(
                    'advanced_times'    => false,
                    'show_booking_link' => true
            ));
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
                // Hooks for custom columns
                ->addAction('manage_edd_booking_posts_columns', $this, 'registerCustomColumns')
                ->addAction('manage_posts_custom_column', $this, 'renderCustomColumns', 10, 2)
                // Hooks for row actions
                ->addFilter('post_row_actions', $this, 'filterRowActions', 10, 2)
                // Hook to force single column display
                ->addFilter('get_user_option_screen_layout_edd_booking', $this, 'setScreenLayout')
                // Disable autosave by dequeueing the autosave script for this cpt
                ->addAction('admin_print_scripts', $this, 'disableAutosave')
                // Hook to create bookings on purchase completion
                ->addAction('edd_complete_purchase', $this, 'createFromPayment')
                // Hook to show bookings in receipt
                ->addAction('edd_payment_receipt_after_table', $this, 'renderBookingsInfoReceipt', 10, 2)
                // Show booking info on Orders page
                ->addAction('edd_view_order_details_files_after', $this, 'renderBookingInfoOrdersPage')
                // AJAX handlers
                ->addAction('wp_ajax_edd_bk_get_bookings_for_calendar', $this, 'getAjaxBookingsForCalendar')
                ->addAction('wp_ajax_edd_bk_get_bookings_info', $this, 'getAjaxBookingInfo');
        
        if (EDD_BK_CALENDAR_ENABLED) {
            $this->getPlugin()->getHookManager()
                    // Show calendar button in table page
                    ->addAction('manage_posts_extra_tablenav', $this, 'renderCalendarButton')
                    // Registers menu items
                    ->addAction('admin_menu', $this, 'registerMenu');
        }
            
    }

}
