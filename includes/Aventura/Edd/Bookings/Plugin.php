<?php

namespace Aventura\Edd\Bookings;

use \Aventura\Edd\Bookings\Controller\ServiceController;

/**
 * Main plugin class.
 *
 * @author Miguel Muscat <miguelmuscat93@gmail.com>
 */
class Plugin
{

    /**
     * The factory that is used to create this instance and its components.
     * 
     * @var Factory
     */
    protected $_factory;

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
     * Creates a new instance.
     * 
     * @param array $data Optional array of data. Default: array()
     */
    public function __construct(array $data = array())
    {
        // Load the EDD license handler and create the license handler instance
        if (class_exists('EDD_License')) {
            $this->license = new \EDD_License(EDD_BK, EDD_BK_PLUGIN_NAME, EDD_BK_VERSION, EDD_BK_AUTHOR);
        }
        // Define WordPress hooks
        $this->hook();
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
     * Checks for plugin dependancies.
     */
    public function checkPluginDependancies()
    {
        if (!class_exists(EDD_BK_PARENT_PLUGIN_CLASS)) {
            $this->deactivate('The <strong>Easy Digital Downloads</strong> plugin must be installed and activated.');
        } else if (\version_compare(EDD_VERSION, EDD_BK_PARENT_PLUGIN_MIN_VERSION, '<')) {
            $this->deactivate(
                    sprintf(
                            'The <strong>Easy Digital Downloads</strong> plugin must be at version %s or later',
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
        // load plugins.php file from WordPress if not loaded
        require_once(ABSPATH . 'wp-admin/includes/plugin.php');
        deactivate_plugins(EDD_BK_BASE);
        if (!is_null($arg)) {
            if (is_callable($arg)) {
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
     * Adds the WordPress hooks.
     */
    public function hook()
    {
        $this->getHookManager()
                ->addAction('admin_init', $this, 'checkPluginDependancies')
                ->addAction('plugins_loaded', $this->getI18n(), 'loadTextdomain');
    }

}
