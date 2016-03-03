<?php

namespace Aventura\Edd\Bookings\Controller;

use \Aventura\Edd\Bookings\Factory\FactoryAbstract;
use \Aventura\Edd\Bookings\Plugin;

/**
 * ControllerAbstract
 *
 * @author Miguel Muscat <miguelmuscat93@gmail.com>
 */
abstract class ControllerAbstract
{
    
    /**
     * The parent plugin instance.
     * 
     * @var Plugin
     */
    protected $_plugin;
    
    /**
     * The factory to use to create instances.
     * 
     * @var FactoryAbstract
     */
    protected $_factory;
    
    /**
     * Constructs a new instance.
     * 
     * @param Plugin $plugin The parent plugin instance.
     */
    public function __construct(Plugin $plugin)
    {
        $this->setPlugin($plugin);
    }
    
    /**
     * Gets the parent plugin instance.
     * 
     * @return Plugin
     */
    public function getPlugin()
    {
        return $this->_plugin;
    }

    /**
     * Sets the parent plugin instance.
     * 
     * @param Plugin $plugin The parent plugin instance.
     * @return ControllerAbstract This instance.
     */
    public function setPlugin(Plugin $plugin)
    {
        $this->_plugin = $plugin;
        return $this;
    }
    
    /**
     * Registers the WordPress hooks.
     */
    abstract public function hook();

}
