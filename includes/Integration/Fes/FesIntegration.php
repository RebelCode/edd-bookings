<?php

namespace Aventura\Edd\Bookings\Integration\Fes;

use \Aventura\Edd\Bookings\Controller\AssetsController;
use \Aventura\Edd\Bookings\Integration\Core\IntegrationAbstract;
use \Aventura\Edd\Bookings\Integration\Fes\Dashboard\DashboardPageInterface;
use \Aventura\Edd\Bookings\Model\Booking;
use \Aventura\Edd\Bookings\Plugin;
use \Aventura\Edd\Bookings\Utils\ArrayUtils;
use \EddBkAssetsConfig;

/**
 * Integration for the FrontEnd Submissions extension.
 *
 * @author Miguel Muscat <miguelmuscat93@gmail.com>
 */
class FesIntegration extends IntegrationAbstract
{

    const UPLOADS_DIRECTORY = 'fes';

    const CALENDAR_THEME_FILE_BASENAME = 'jquery-ui.theme';

    /**
     * The dashboard pages.
     *
     * @var DashboardPageInterface[]
     */
    protected $dashboardPages;

    /**
     * Assets configuration.
     *
     * @var EddBkAssetsConfig
     */
    protected $assetsConfig;

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
     * Gets the assets configuration instance.
     *
     * @return EddBkAssetsConfig
     */
    public function getAssetsConfig()
    {
        return $this->assetsConfig;
    }

    /**
     * Sets the assets configuration instance.
     *
     * @param EddBkAssetsConfig $assetsConfig The new instance.
     * @return FesIntegration This instance.
     */
    public function setAssetsConfig(EddBkAssetsConfig $assetsConfig)
    {
        $this->assetsConfig = $assetsConfig;
        return $this;
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
            ->addAction('fes_payment_receipt_after_table', $this, 'bookingInfoOrderDetailsPage')
        ;

        $this->getAssetsConfig()->loadFile(EDD_BK_FES_CONFIG_DIR . 'assets.xml');
        $this->getPlugin()->getAssetsController()->nq($this, 'enqueueAssets');

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
        static::checkUploadsDirectory();
        return $this;
    }

    /**
     * Enqueues the assets.
     *
     * @param array $assets
     * @param string $ctx
     * @param AssetsController $c
     * @return array
     */
    public function enqueueAssets(array $assets, $ctx, AssetsController $c)
    {
        switch ($ctx) {
            case AssetsController::CONTEXT_FRONTEND:
                $assets = array_merge($assets, $this->getFrontendAssets($c));
                break;
        }

        return $assets;
    }

    /**
     * Gets the frontend assets.
     *
     * @param AssetsController $c
     * @return array
     */
    public function getFrontendAssets(AssetsController $c)
    {
        $assets = array(
            'eddbk.js.fes.frontend',
            'eddbk.css.fes.frontend',
            'eddbk.css.bookings.calendar',
            'eddbk.js.bookings.calendar',
            'eddbk.css.lib.fullcalendar'
        );

        $c->attachScriptData('eddbk.js.bookings.calendar', 'BookingsCalendar', array(
            'postEditUrl' => admin_url('post.php?post=%s&action=edit'),
            'theme'       => !is_admin(),
            'fesLinks'    => !is_admin()
        ));

        $calendarThemeUri = static::getCalendarThemeStylesheetUrl();
        if ($calendarThemeUri !== false) {
            $c->addAsset(AssetsController::TYPE_STYLE, 'eddbk.css.fes.calendarTheme', $calendarThemeUri);
            $assets[] = 'eddbk.css.fes.calendarTheme';
        }

        return $assets;
    }

    /**
     * Renders a booking info on the edit orders FES page.
     *
     * @param WP_Post $payment The payment post object.
     */
    public function bookingInfoOrderDetailsPage($payment)
    {
        echo eddBookings()->renderView('Fes.Dashboard.Orders.Bookings', compact('payment'));
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

    /**
     * Gets the uploads directory path.
     *
     * @return string
     */
    public static function getUploadsDirectory()
    {
        return implode(DIRECTORY_SEPARATOR, array(EDD_BK_UPLOADS_DIR, static::UPLOADS_DIRECTORY));
    }

    /**
     * Gets the uploads directory URL.
     *
     * @return string
     */
    public static function getUploadsUrl()
    {
        return implode('/', array(EDD_BK_UPLOADS_URL, static::UPLOADS_DIRECTORY));
    }

    /**
     * Checks if the uploads directory exists, creating it if not.
     */
    public static function checkUploadsDirectory()
    {
        $path = static::getUploadsDirectory();
        if (!file_exists($path)) {
            wp_mkdir_p($path);
        }
    }

    /**
     * Gets the URL for the calendar theme stylesheet.
     *
     * @return string|boolean The URL to the stylesheet or false if no stylesheet is available.
     */
    public static function getCalendarThemeStylesheetUrl()
    {
        // Prepare base dir and URL
        $uploadsUrl = static::getUploadsUrl();
        $uploadsDirectory = static::getUploadsDirectory();
        // Prepare file names
        $regularFileName = sprintf('%s.css', static::CALENDAR_THEME_FILE_BASENAME);
        $minifiedFileName = sprintf('%s.min.css', static::CALENDAR_THEME_FILE_BASENAME);
        // Generate file paths
        $regularFilePath = $uploadsDirectory . DIRECTORY_SEPARATOR . $regularFileName;
        $minifiedFilePath = $uploadsDirectory . DIRECTORY_SEPARATOR . $minifiedFileName;
        // Generate file URLs
        $regularFileUrl = sprintf('%s/%s', $uploadsUrl, $regularFileName);
        $minifiedFileUrl = sprintf('%s/%s', $uploadsUrl, $minifiedFileName);
        // Prepare existence flags
        $regularFileExists = file_exists($regularFilePath);
        $minifiedFileExists = file_existS($minifiedFilePath);

        // Check for WordPress' SCRIPT_DEBUG constant
        $scriptDebug = defined('SCRIPT_DEBUG') && SCRIPT_DEBUG;

        // Return minified file URL if:
        // - the minified file exists
        // - script debug is TRUE but the regular file does not exist
        if ($minifiedFileExists && ($scriptDebug || !$regularFileExists)) {
            return $minifiedFileUrl;
        }
        // Return the regular file URL if:
        // - the regular file exists
        // - script debug is FALSE but the minified file does not exist
        if ($regularFileExists && (!$scriptDebug || !$minifiedFileExists)) {
            return $regularFileUrl;
        }
        // Return false if neither file exists
        return false;
    }

}
