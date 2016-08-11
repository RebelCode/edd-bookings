<?php

namespace Aventura\Edd\Bookings\Integration\Fes;

use \Aventura\Edd\Bookings\Integration\Core\IntegrationAbstract;
use \Aventura\Edd\Bookings\Integration\Fes\Dashboard\DashboardPageInterface;
use \Aventura\Edd\Bookings\Model\Booking;
use \Aventura\Edd\Bookings\Plugin;
use \Aventura\Edd\Bookings\Utils\ArrayUtils;

/**
 * Integration for the FrontEnd Submissions extension.
 *
 * @author Miguel Muscat <miguelmuscat93@gmail.com>
 */
class FesIntegration extends IntegrationAbstract
{

    /**
     * The dashboard pages.
     * 
     * @var DashboardPageInterface[]
     */
    protected $dashboardPages;

    /**
     * Constructs a new instance.
     * 
     * @param Plugin $plugin The parent plugin instance.
     */
    public function __construct(Plugin $plugin = null)
    {
        parent::__construct($plugin);
        $this->dashboardPages = array();
    }

    /**
     * Gets the dashboard pages.
     * 
     * @return array The dashboard pages.
     */
    public function getDashboardPages()
    {
        return $this->dashboardPages;
    }

    /**
     * Adds a dashboard page.
     * 
     * @param DashboardPageInterface $dashboardPage The page to add.
     * @return FesIntegration This instance.
     */
    public function addDashboardPage(DashboardPageInterface $dashboardPage)
    {
        $this->dashboardPages[] = $dashboardPage;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function hook()
    {
        $this->getPlugin()->getHookManager()
            ->addAction('fes_load_fields_require', $this, 'init')
            ->addAction('fes_payment_receipt_after_table', $this, 'bookingInfoOrderDetailsPage');
        return $this;
    }

    /**
     * Initializes the integration.
     * 
     * Checks for FES extension and its version to decide whether or not to proceed with integration.
     */
    public function init()
    {
        if (!class_exists('EDD_Front_End_Submissions')) {
            return;
        }
        if (!static::isFesLoaded()) {
            return;
        }
        add_filter('fes_load_fields_array', array($this, 'registerFields'));
        foreach ($this->getDashboardPages() as $page) {
            $page->hook();
        }
        return $this;
    }

    /**
     * Renders a booking info on the edit orders FES page.
     *
     * @param WP_Post $payment The payment post object.
     */
    public function bookingInfoOrderDetailsPage($payment)
    {
        echo eddBookings()->getBookingController()->getPostType()->renderBookingInfoOrdersPage($payment, array(
            'booking_details_url' => add_query_arg(
                array(
                    'task'       => 'edit-booking',
                    'booking_id' => '%s'
                ), get_permalink()
            )
        ));
    }

    /**
     * Filters the FES fields to register out custom fields.
     * 
     * The given fields array is an associative array of field ID and classname pairs.
     * 
     * @param array $fields The input fields.
     * @return The output fields.
     */
    public function registerFields($fields)
    {
        $classes = apply_filters('edd_bk_fes_fields', $this->getFieldClasses());
        return array_merge($fields, $classes);
    }

    /**
     * Gets the classes for the FES Fields.
     * 
     * @return array An array of field ID and fully qualified class name pairs.
     */
    public function getFieldClasses()
    {
        return array(
            'edd_bk' => 'Aventura\\Edd\\Bookings\\Integration\\Fes\\Field\\BookingsField'
        );
    }

    /**
     * Gets the bookings for a particular user vendor.
     *
     * @param integer|boolean  $userId The user ID. False for current logged in user.
     * @return Booking[] Array of bookings.
     */
    public function getBookingsForUser($userId = false)
    {
        $products = EDD_FES()->vendors->get_published_products($userId);
        // Because for some reason, FES returns FALSE instead of an empty array
        $actualProducts = is_array($products)
            ? $products
            : array();
        $productIds = ArrayUtils::arrayColumn($actualProducts, 'ID');
        return $this->getPlugin()->getBookingController()->getBookingsForService($productIds);
    }

    /**
     * Checks if the FES plugin is loaded and is at least at the required version.
     * 
     * @return boolean True if FES is loaded and is at least at the required version, false otherwise.
     */
    public static function isFesLoaded()
    {
        return defined('fes_plugin_version') && version_compare(fes_plugin_version, 2.3, '>=');
    }

    /**
     * Checks if the current page is an FES admin page.
     * 
     * @return boolean
     */
    public static function isFesAdminPage()
    {
        if (!fes_is_admin()) {
            return false;
        }

        $screen = \get_current_screen();
        if (!is_object($screen)) {
            return false;
        }

        $postTypes = array('fes-forms', 'download');
        $screens = array('profile', 'user-edit', 'user', 'user-new');

        return (isset($screen->base) && substr($screen->base, 0, 7) === 'edd-fes') ||
            (isset($screen->post_type) && in_array($screen->post_type, $postTypes)) ||
            (isset($screen->id) && in_array($screen->id, $screens));
    }

    /**
     * Gets the FES frontend page ID.
     * 
     * @return string
     */
    public static function getFesFrontendPage()
    {
        return EDD_FES()->helper->get_option('fes-vendor-dashboard-page', false);
    }

    /**
     * Checks if the current page is an FES frontend page.
     * 
     * @return boolean
     */
    public static function isFesFrontendPage()
    {
        return fes_is_frontend() && is_page(static::getFesFrontendPage());
    }

}
