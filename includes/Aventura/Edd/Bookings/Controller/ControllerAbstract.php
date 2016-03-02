<?php

namespace Aventura\Edd\Bookings\Controller;

use \Aventura\Edd\Bookings\Factory\ModelCptFactoryAbstract;
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
     * @var ModelCptFactoryAbstract
     */
    protected $_factory;
    
    /**
     * Constructs a new instance.
     * 
     * @param Plugin $plugin The parent plugin instance.
     */
    public function __construct(Plugin $plugin, ModelCptFactoryAbstract $factory)
    {
        $this->setPlugin($plugin)
                ->setFactory($factory);
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
     * Gets the factory.
     * 
     * @return ModelCptFactoryAbstract
     */
    public function getFactory()
    {
        return $this->_factory;
    }

    /**
     * Sets the factory.
     * 
     * @param ModelCptFactoryAbstract $factory The factory that is used to create instances.
     * @return ControllerAbstract This instance.
     */
    public function setFactory(ModelCptFactoryAbstract $factory)
    {
        $this->_factory = $factory;
        return $this;
    }

        
    /**
     * Gets a single object by ID.
     * 
     * @param integer $id The ID of the object to retrieve.
     */
    abstract public function get($id);
    
    /**
     * Queries the objects in the database.
     * 
     * @param array $query Optional query array that defines what objects are retrieved. If an empty array is given, all
     *                     objects are returned.
     * @return array An array of objects that matched the query.
     */
    abstract public function query(array $query = array());
    
    /**
     * Registers the WordPress hooks.
     */
    abstract public function hook();

}
