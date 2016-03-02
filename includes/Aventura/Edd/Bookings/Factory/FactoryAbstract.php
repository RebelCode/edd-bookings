<?php

namespace Aventura\Edd\Bookings\Factory;

use \Aventura\Edd\Bookings\Plugin;

/**
 * Basic customizable single-class generator factory.
 *
 * @author Miguel Muscat <miguelmuscat93@gmail.com>
 */
abstract class FactoryAbstract implements FactoryInterface
{
    
    /**
     * Default class name to use.
     */
    const DEFAULT_CLASSNAME = '';
    
    /**
     * The name of the class to instantiate.
     * 
     * @var string
     */
    protected $_className;
    
    /**
     * The parent plugin.
     * 
     * @var Plugin
     */
    protected $_plugin;
    
    /**
     * Constructs a new instance.
     * 
     * @param Plugin $plugin The parent plugin instance.
     */
    public function __construct(Plugin $plugin)
    {
        $this->setClassName(static::DEFAULT_CLASSNAME)
                ->setPlugin($plugin);
    }
    
    /**
     * Gets the name of the class to instantiate.
     * 
     * @return string
     */
    public function getClassName()
    {
        return $this->_className;
    }
    
    /**
     * Sets the name of the class to instantiate.
     * 
     * @param string $className The name of the class to instantiate.
     * @return FactoryAbstract This instance.
     */
    public function setClassName($className)
    {
        $this->_className = $className;
        return $this;
    }
    
    /**
     * {@inheritdoc}
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
     * @return FactoryAbstract This instance.
     */
    public function setPlugin(Plugin $plugin)
    {
        $this->_plugin = $plugin;
        return $this;
    }

}
