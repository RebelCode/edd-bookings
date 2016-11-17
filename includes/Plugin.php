<?php

namespace Aventura\Edd\Bookings;

use \Aventura\Diary\DateTime;
use \Aventura\Diary\DateTime\Duration;
use \Aventura\Edd\Bookings\Controller\BookingController;
use \Aventura\Edd\Bookings\Controller\ServiceController;
use \Aventura\Edd\Bookings\Integration\Core\IntegrationInterface;
use \Aventura\Edd\Bookings\Renderer\MainPageRenderer;
use \Aventura\Edd\Bookings\Settings\Settings;

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
     * The ajax controller.
     *
     * @var AjaxController
     */
    protected $_ajax;

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
     * The settings.
     *
     * @var Settings
     */
    protected $_settings;

    /**
     * The cart controller instance.
     *
     * @var Controller\CartController
     */
    protected $_cartController;

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
     * Gets the plugin ID (or slug).
     *
     * @return string
     */
    public function getId()
    {
        return EDD_BK_PLUGIN_ID;
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
     * Gets the settings controller.
     *
     * @return Settings
     */
    public function getSettings()
    {
        if (is_null($this->_settings)) {
            $this->_settings = $this->getFactory()->createSettings();
        }

        return $this->_settings;
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
     * Gets the ajax controller.
     * 
     * @return Controller\AjaxController
     */
    public function getAjaxController()
    {
        if (is_null($this->_ajax)) {
            $this->_ajax = $this->getFactory()->createAjaxController();
        }

        return $this->_ajax;
    }

    /**
     * Gets the assets controller.
     * 
     * @return Controller\AssetsController
     */
    public function getAssetsController()
    {
        if (is_null($this->_assets)) {
            $this->_assets = $this->getFactory()->createAssetsController();
        }
        return $this->_assets;
    }

    /**
     * Gets the cart controller.
     *
     * @return Controller\CartController
     */
    public function getCartController()
    {
        if (is_null($this->_cartController)) {
            $this->_cartController = $this->getFactory()->createCartController();
        }
        return $this->_cartController;
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
        $maintitle = __('Bookings', 'eddbk');
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
        $minCapability = apply_filters('edd_bk_menu_capability', 'manage_shop_settings');
        $menuSlug = $this->getMenuSlug();

        // Add settings submenu item (links to EDD extension page with Bookings tab selected)
        global $submenu;
        $submenu[$menuSlug][] = array(
            __('Settings', 'eddbk'),
            $minCapability,
            admin_url('edit.php?post_type=download&page=edd-settings&tab=extensions&section=eddbk')
        );

        // Prepare vars
        $subTitle = __('About', 'eddbk');
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
        // Check PHP version
        if (version_compare(PHP_VERSION, EDD_BK_MIN_PHP_VERSION, '<')) {
            $this->deactivate(
                sprintf(
                    '%s <strong><code>%s</code></strong>',
                    __('The EDD Bookings plugin failed to activate. Minimum PHP version required:', 'eddbk'),
                    EDD_BK_MIN_PHP_VERSION
                )
            );
        }
        // Check WordPress version
        if (version_compare(\get_bloginfo('version'), EDD_BK_MIN_WP_VERSION, '<')) {
            $this->deactivate(
                sprintf(
                    '%s <strong><code>%s</code></strong>',
                    __('The EDD Bookings plugin failed to activate. Minimum WordPress version required:', 'eddbk'),
                    EDD_BK_MIN_WP_VERSION
                )
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
            $this->deactivate(
                __('The EDD Bookings plugin has been deactivated. The Easy Digital Downloads plugin must be installed and activated.', 'eddbk')
            );
        } else if (version_compare(EDD_VERSION, EDD_BK_PARENT_PLUGIN_MIN_VERSION, '<')) {
            $this->deactivate(
                \sprintf(
                    '%s <strong><code>%s</code></strong>',
                    __('The EDD Bookings plugin has been deactivated. Minimum Easy Digital Downloads version required:', 'eddbk'),
                    EDD_BK_PARENT_PLUGIN_MIN_VERSION
                )
            );
        }
    }

    /**
     * Deactivates this plugin.
     *
     * @param callbable|string $arg The notice callback function (that will be hooked on `admin_notices` after
     *                              deactivation, or a string specifying the reason for deactivation. Default: null
     */
    public function deactivate($arg = null)
    {
        // Remove activation transient for redirect if it exists
        delete_transient(static::ACTIVATION_TRANSIENT);

        // load plugins.php file from WordPress if not loaded
        require_once(ABSPATH . 'wp-admin/includes/plugin.php');
        // Deactivate
        \deactivate_plugins(EDD_BK_BASE);

        // Arg is null, we are done
        if (is_null($arg)) {
            return;
        }
        // Get the message from the arg
        $message = is_callable($arg)
            ? call_user_func($arg)
            : $arg;
        $title = __('EDD Bookings has been deactivated!', 'eddbk');
        $fullMessage = sprintf('<h1>%s</h1><p>%s</p>', $title, $message);
        // Show wp_die screen with back link
        wp_die($fullMessage, $title, array(
            'back_link' => true
        ));
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
    public function addIntegration($key, IntegrationInterface $integration)
    {
        $integration->setPlugin($this);
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
            ->addAction('init', $this->getI18n(), 'loadTextDomain')
            ->addAction('admin_menu', $this, 'registerMenu')
            ->addAction('admin_menu', $this, 'registerSubMenus', 100)
            ->addAction('admin_init', $this, 'maybeDoWelcomePageRedirection')
        ;
        $this->getAssetsController()->nq($this, 'enqueueAssets');
        $this->getAjaxController()->hook();
        $this->getSettings()->hook();
        $this->getBookingController()->hook();
        $this->getServiceController()->hook();
        $this->getAssetsController()->hook();
        $this->getCartController()->hook();
        $this->getPatcher()->hook();
        // Hook all integrations
        foreach($this->getIntegrations() as $integration) {
            $integration->hook();
        }
    }

    /**
     * Enqueues the core asset files.
     *
     * @param array $assets
     * @param string $ctx
     * @param Controller\AssetsController $c
     * @return array
     */
    public function enqueueAssets($assets, $ctx, $c)
    {
        switch ($ctx) {
            case Controller\AssetsController::CONTEXT_BACKEND:
            case Controller\AssetsController::CONTEXT_FRONTEND:
                $assets = array_merge($assets, $this->getCoreAssets($c));
                break;

        }

        return $assets;
    }

    /**
     * Gets the backend assets for the current backend page.
     *
     * @param \Aventura\Edd\Bookings\Controller\AssetsController $c The assets controller.
     * @return array The asset handles.
     */
    public function getCoreAssets(Controller\AssetsController $c)
    {
        $c->attachScriptData('eddbk.js.ajax', 'Ajax', array(
            'url' => admin_url('admin-ajax.php')
        ));
        $c->attachScriptData('eddbk.js.utils', 'Utils', array(
            'unitLabels' => Utils\UnitUtils::getUnitLabels(true)
        ));

        $assets = array(
            'eddbk.js.class',
            'eddbk.js.ajax',
            'eddbk.js.utils',
            'eddbk.js.widget',
            'eddbk.js.notices',
            'eddbk.js.service',
            'eddbk.js.availability',
            'eddbk.css.lib.font-awesome'
        );

        if (function_exists('get_current_screen') && get_current_screen()->id === 'toplevel_page_edd-bookings') {
            $assets[] = 'eddbk.css.about';
        }

        return $assets;
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
     * Renders a view by name.
     * 
     * A view name is a string containing dot-separated parts that reflect the directory structure in the views folder.
     * 
     * @param string $viewName The view name.
     * @param array $data Array of data to pass to the view.
     * @return string The rendered content.
     */
    public function renderView($viewName, $data)
    {
        $viewpath = $this->getViewFilePath($viewName);
        ob_start();
        include $viewpath;
        return ob_get_clean();
    }
    
    /**
     * Gets the file path for a given view name.
     * 
     * @param string $viewName The view name.
     * @return string The file path.
     */
    public function getViewFilePath($viewName)
    {
        $parts = array_map('trim', explode('.', $viewName));
        return sprintf('%s%s.php', EDD_BK_VIEWS_DIR, implode(DIRECTORY_SEPARATOR, $parts));
    }

    /**
     * Renders an admin tooltip.
     *
     * Requires the tooltip assets to be enqueued.
     *
     * @param string $text The tooltip markup.
     * @param string $icon The tooltip font-awesome icon. Default: question-circle
     */
    public function adminTooltip($text, $icon = null)
    {
        $nText = nl2br(trim($text));
        $nIcon = is_null($icon)
            ? 'question-circle'
            : $icon;
        $data = array(
            'text' => $nText,
            'icon' => $nIcon
        );

        return $this->renderView('Admin.Tooltip', $data);
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
    
    /**
     * Loads all files in a directory.
     * 
     * @param string $dir The directory.
     * @param string $extension The extension of the files to load.
     * @return boolean True on success, false on failure.
     */
    public function loadDirectory($dir, $extension = 'php')
    {
        $dirParts = preg_split('| \/ \\ |x', $dir, -1, PREG_SPLIT_NO_EMPTY);
        array_unshift($dirParts, EDD_BK_DIR);
        array_push($dirParts, sprintf('*.%s', $extension));
        $path = implode(DIRECTORY_SEPARATOR, $dirParts);
        $entries = glob($path);
        if (!is_array($entries)) {
            return false;
        }
        foreach ($entries as $filename) {
            include_once $filename;
        }
        return true;
    }

    /**
     * Loads a configuration file from the config directory.
     *
     * @param string $filename The name of the config file, without the extenstion.
     * @return mixed The configuration data or null if the file does not exist.
     */
    public function loadConfigFile($filename)
    {
        return $this->loadXmlFile($this->getConfigFilePath($filename));
    }

    /**
     * Gets the full path of a config file in the config directory.
     *
     * @param string $filename The filename, without extension.
     * @return string The full file path.
     */
    public function getConfigFilePath($filename)
    {
        return sprintf('%s%s.xml', EDD_BK_CONFIG_DIR, $filename);
    }

    /**
     * Loads an XML file.
     *
     * @param string $filepath The full path to the file.
     * @return mixed The loaded parsed XML (as SimpleXML) or null if the file could not be opened.
     */
    public function loadXmlFile($filepath)
    {
        return (file_exists($filepath) && is_readable($filepath))
            ? simplexml_load_file($filepath)
            : null;
    }

}
