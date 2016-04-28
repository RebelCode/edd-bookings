<?php

namespace Aventura\Edd\Bookings;

use \Aventura\Diary\DateTime;
use \Aventura\Diary\DateTime\Duration;
use \Aventura\Edd\Bookings\Controller\ScheduleController;
use \Aventura\Edd\Bookings\Controller\BookingController;
use \Aventura\Edd\Bookings\Controller\ServiceController;
use \Aventura\Edd\Bookings\Controller\TimetableController;
use \Aventura\Edd\Bookings\Integration\IntegrationInterface;
use \Aventura\Edd\Bookings\Renderer\MainPageRenderer;

/**
 * Main plugin class.
 *
 * This class uses a Factory class to instantiate it's members (controllers, i18n, assets, hook manager, etc).
 * 
 * @author Miguel Muscat <miguelmuscat93@gmail.com>
 */
class Plugin
{

    const ACTIVATION_TRANSIENT = 'edd_bk_activation_transient';
    
    /**
     * The factory that is used to create this instance and its components.
     * 
     * @var Factory
     */
    protected $_factory;

    /**
     * The services controller.
     * 
     * @var ServiceController
     */
    protected $_serviceController;
    
    /**
     * The bookings controller.
     * 
     * @var BookingController
     */
    protected $_bookingController;
    
    /**
     * The schedules controller.
     * 
     * @var ScheduleController
     */
    protected $_scheduleController;
    
    /**
     * The timetable controller.
     * 
     * @var TimetableController
     */
    protected $_timetableController;
    
    /**
     * The assets controller.
     * 
     * @var Assets
     */
    protected $_assets;
    
    /**
     * Internationalization class.
     * 
     * @var I18n
     */
    protected $_i18n;

    /**
     * The hook manager.
     * 
     * @var HookManager
     */
    protected $_hookManager;

    /**
     * String used to cache the reason for deactivation.
     * 
     * @var string
     */
    protected $_deactivationReason;

    /**
     * List of integrations.
     * 
     * @var array
     */
    protected $_integrations;
    
    /**
     * The patcher instance.
     * 
     * @var Patcher
     */
    protected $_patcher;
    
    /**
     * Creates a new instance.
     * 
     * @param array $data Optional array of data. Default: array()
     */
    public function __construct(array $data = array())
    {
        // Load the EDD license handler and create the license handler instance
        if (class_exists('EDD_License')) {
            $this->license = new \EDD_License(EDD_BK, EDD_BK_PLUGIN_NAME, EDD_BK_VERSION, EDD_BK_PLUGIN_AUTHOR);
        }
        $this->_integrations = array();
    }

    /**
     * Gets the factory.
     * 
     * @return Factory
     */
    public function getFactory()
    {
        return $this->_factory;
    }

    /**
     * Sets the factory.
     * 
     * @param Factory $factory The factory.
     * @return Plugin This instance.
     */
    public function setFactory(Factory $factory)
    {
        $this->_factory = $factory;
        return $this;
    }
    
    /**
     * Gets the service controller.
     * 
     * @return ServiceController
     */
    public function getServiceController()
    {
        if (is_null($this->_serviceController)) {
            $this->_serviceController = $this->getFactory()->createServiceController();
        }
        return $this->_serviceController;
    }
    
    /**
     * Gets the booking controller.
     * 
     * @return BookingController
     */
    public function getBookingController()
    {
        if (is_null($this->_bookingController)) {
            $this->_bookingController = $this->getFactory()->createBookingController();
        }
        return $this->_bookingController;
    }
    
    /**
     * Gets the schedules controller.
     * 
     * @return ScheduleController
     */
    public function getScheduleController()
    {
        if (is_null($this->_scheduleController)) {
            $this->_scheduleController = $this->getFactory()->createScheduleController();
        }
        return $this->_scheduleController;
    }
    
    /**
     * Gets the timetable controller.
     * 
     * @return TimetableController
     */
    public function getTimetableController()
    {
        if (is_null($this->_timetableController)) {
            $this->_timetableController = $this->getFactory()->createTimetableController();
        }
        return $this->_timetableController;
    }

    /**
     * Gets the assets controller.
     * 
     * @return Assets
     */
    public function getAssets()
    {
        if (is_null($this->_assets)) {
            $this->_assets = $this->getFactory()->createAssetsController();
        }
        return $this->_assets;
    }
    
    /**
     * Gets the internationalization class.
     * 
     * @return I18n
     */
    public function getI18n()
    {
        if (is_null($this->_i18n)) {
            $this->_i18n = $this->getFactory()->createI18n();
        }
        return $this->_i18n;
    }

    /**
     * Gets the patcher instance.
     * 
     * @return Patcher
     */
    public function getPatcher()
    {
        if (is_null($this->_patcher)) {
            $this->_patcher = $this->getFactory()->createPatcher();
        }
        return $this->_patcher;
    }
    
    /**
     * Gets the hook manager.
     * 
     * @return HookManager
     */
    public function getHookManager()
    {
        if (is_null($this->_hookManager)) {
            $this->_hookManager = $this->getFactory()->createHookManager();
        }
        return $this->_hookManager;
    }

    /**
     * Gets the slug of the top-level WordPress admin menu.
     * 
     * @return string
     */
    public function getMenuSlug()
    {
        return \apply_filters('edd_bk_menu_slug', 'edd-bookings');
    }
    
    /**
     * Registers the top-level WordPress admin menu.
     */
    public function registerMenu()
    {
        // Prepare vars
        $textDomain = $this->getI18n()->getDomain();
        $maintitle = __('EDD Bookings', $textDomain);
        $menuSlug = $this->getMenuSlug();
        $menuPos = \apply_filters('edd_bk_menu_pos', 26);
        $menuIcon = \apply_filters('edd_bk_menu_icon', 'dashicons-calendar');
        $minCapability = apply_filters('edd_bk_menu_capability', 'manage_shop_settings');
        // Add the top-level menu
        \add_menu_page($maintitle, $maintitle, $minCapability, $menuSlug, null, $menuIcon, $menuPos);
    }
    
    /**
     * Registers the second-level WordPress admin menus.
     */
    public function registerSubmenus()
    {
        // Prepare vars
        $textDomain = $this->getI18n()->getDomain();
        $menuSlug = $this->getMenuSlug();
        $subTitle = __('About', $textDomain);
        $minCapability = apply_filters('edd_bk_menu_capability', 'manage_shop_settings');
        $callback = array($this, 'renderMainPage');
        // Add the "About" submenu, with the same slug to replace "EDD Bookings" entry from previous line
        \add_submenu_page($menuSlug, $subTitle, $subTitle, $minCapability, $menuSlug, $callback);
    }
    
    /**
     * Renders the main page.
     */
    public function renderMainPage()
    {
        $renderer = new MainPageRenderer($this);
        echo $renderer->render();
    }
    
    /**
     * Callback function triggered when the plugin is activated.
     *
     * @since 1.0.0
     */
    public function onActivate()
    {
        if (version_compare(\get_bloginfo('version'), EDD_BK_MIN_WP_VERSION, '<')) {
            $this->deactivate();
            \wp_die(
                    \sprintf(
                            'The EDD Bookings plugin failed to activate: WordPress version must be %s or later.',
                            EDD_BK_MIN_WP_VERSION
                    ), 'Error', array('back_link' => true)
            );
        }
        // Set transient for redirection to welcome page
        set_transient(static::ACTIVATION_TRANSIENT, true, 30);
        
        do_action('edd_bk_activated');
    }

    /**
     * Callback function triggered when the plugin is deactivated.
     *
     * @since 1.0.0
     */
    public function onDeactivate()
    {
        // Do nothing
    }

    /**
     * Checks for plugin dependancies.
     */
    public function checkPluginDependancies()
    {
        if (!\class_exists(EDD_BK_PARENT_PLUGIN_CLASS)) {
            $this->deactivate('The <strong>Easy Digital Downloads</strong> plugin must be installed and activated.');
        } else if (version_compare(EDD_VERSION, EDD_BK_PARENT_PLUGIN_MIN_VERSION, '<')) {
            $this->deactivate(
                    \sprintf(
                            'The <strong>Easy Digital Downloads</strong> plugin must be at version %s or later', EDD_BK_PARENT_PLUGIN_MIN_VERSION
                    )
            );
        }
    }

    /**
     * Deactivates this plugin.
     *
     * @param \callbable|string $arg The notice callback function (that will be hooked on `admin_notices` after
     *                              deactivation, or a string specifying the reason for deactivation. Default: null
     */
    public function deactivate($arg = null)
    {
        // load plugins.php file from WordPress if not loaded
        require_once(ABSPATH . 'wp-admin/includes/plugin.php');
        \deactivate_plugins(EDD_BK_BASE);
        if (!\is_null($arg)) {
            if (\is_callable($arg)) {
                $this->getHookManager()->addAction('admin_notices', null, $arg);
            } else {
                $this->_deactivationReason = $arg;
                $this->getHookManager()->addAction('admin_notices', $this, 'showDeactivationNotice');
            }
        }
    }

    /**
     * Prints an admin notice that tells the user that the plugin has been deactivated, and why.
     *
     * @since 1.0.0
     */
    public function show_deactivation_reason()
    {
        echo '<div class="error notice is-dismissible"><p>';
        echo 'The <strong>EDD Bookings</strong> plugin has been deactivated. ' . $this->_deactivationReason;
        echo '</p><button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button></div>';
    }

    /**
     * Checks if a redirection to the welcome page is due and if so, redirects.
     */
    public function maybeDoWelcomePageRedirection()
    {
        if (get_transient(static::ACTIVATION_TRANSIENT) && !is_network_admin() && !isset($_GET['activate-multi'])) {
            delete_transient(static::ACTIVATION_TRANSIENT);
            wp_safe_redirect(admin_url('admin.php?page=edd-bookings'));
            exit;
        }
    }
    
    /**
     * Adds an integration.
     * 
     * @param string $key The key to use to identify the integration 
     * @param IntegrationInterface $integration The integration.
     * @return Plugin This instance.
     */
    public function addIntegration($key, $integration)
    {
        $this->_integrations[$key] = $integration;
        return $this;
    }
    
    /**
     * Gets an integration.
     * 
     * @param string $key The key of the integration.
     * @return IntegrationInterface
     */
    public function getIntegration($key)
    {
        return $this->hasIntegration($key)
                ? $this->_integrations[$key]
                : null;
    }
    
    /**
     * Gets all the integrations.
     * 
     * @return array An array with array keys of integration keys and array values of IntegrationInterface instances.
     */
    public function getIntegrations()
    {
        return $this->_integrations;
    }
    
    /**
     * Checks if an integration with a given key exists.
     * 
     * @param string $key The key of the integration.
     * @return IntegrationInterface
     */
    public function hasIntegration($key)
    {
        return isset($this->_integrations[$key]);
    }
    
    /**
     * Gets, then removes, an integration.
     * 
     * @param string $key The key of the integration.
     * @return IntegrationInterface|null The removed instance or null if the integration was not found.
     */
    public function removeIntegration($key)
    {
        $integration = null;
        if ($this->hasIntegration($key)) {
            $integration = $this->getIntegration($key);
            unset($this->_integrations[$key]);
        }
        return $integration;
    }
    
    /**
     * Adds the WordPress hooks.
     */
    public function hook()
    {
        $this->getHookManager()
                ->addAction('admin_init', $this, 'checkPluginDependancies')
                ->addAction('plugins_loaded', $this->getI18n(), 'loadTextdomain')
                ->addAction('admin_menu', $this, 'registerMenu')
                ->addAction('admin_menu', $this, 'registerSubMenus', 100)
                ->addAction('admin_init', $this, 'maybeDoWelcomePageRedirection');
        $this->getBookingController()->hook();
        $this->getServiceController()->hook();
        $this->getScheduleController()->hook();
        $this->getTimetableController()->hook();
        $this->getAssets()->hook();
        $this->getPatcher()->hook();
        // Hook all integrations
        foreach($this->getIntegrations() as $integration) {
            $integration->hook();
        }
    }

    /**
     * Gets the server timezone offset, as saved in the WordPress database.
     * 
     * @return integer The saved timezone offset.
     */
    public function getServerTimezoneOffset()
    {
        return intval(\get_option('gmt_offset'));
    }
    
    /**
     * Gets the server timezone offset, in seconds.
     * 
     * @return integer The number of seconds that need to be subtracted from server time to obtain UTC time.
     */
    public function getServerTimezoneOffsetSeconds()
    {
        return Duration::hours(intval($this->getServerTimezoneOffset()), false);
    }
    
    /**
     * Gets the server timezone offset, as a Duration instance.
     * 
     * @return Duration The duration that needs to be subtracted from server time to obtain UTC time.
     */
    public function getServerTimezoneOffsetDuration()
    {
        return Duration::hours(intval($this->getServerTimezoneOffset()));
    }
    
    /**
     * Shifts the given UTC Datetime instance to server time.
     * 
     * @param DateTime $datetime The instance to shift.
     * @return DateTime An instance containing the shifted time.
     */
    public function utcTimeToServerTime(DateTime $datetime)
    {
        return $datetime->copy()->plus($this->getServerTimezoneOffsetDuration());
    }
    
    /**
     * Shifts the given server Datetime instance to UTC time.
     * 
     * @param DateTime $datetime The instance to shift.
     * @return DateTime An instance containing the shifted time.
     */
    public function serverTimeToUtcTime(DateTime $datetime)
    {
        return $datetime->copy()->minus($this->getServerTimezoneOffsetDuration());
    }
    
    /**
     * Used for debugging purposes. Dumps the given data using `wp_die()`.
     * 
     * Note: This method will halt execution!
     * 
     * @uses wp_die()
     * @see wp_die()
     * @param mixed $data The data to debug.
     */
    public function debugDie($data)
    {
        wp_die(sprintf('<pre>%s</pre>', print_r($data, true)));
    }
    
}
