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
     * Creates a new instance.
     * 
     * @param array $data Optional array of data. Default: array()
     */
    public function __construct(array $data = array())
    {
        // Load the EDD license handler and create the license handler instance
		if ( class_exists( 'EDD_License' ) ) {
			$this->license = new \EDD_License(EDD_BK, EDD_BK_PLUGIN_NAME, EDD_BK_VERSION, EDD_BK_AUTHOR);
        }
        // These getters will instantiate the memebers
        $this->getHookManager()
            ->getI18n();
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
     * Adds the WordPress hooks.
     */
    public function hook()
    {
        $this->getHookManager()
                ->addAction('admin_init', $this, 'checkPluginDependancies')
                ->addAction('plugins_loaded', $this->getI18n(), 'loadTextdomain');
    }

}
