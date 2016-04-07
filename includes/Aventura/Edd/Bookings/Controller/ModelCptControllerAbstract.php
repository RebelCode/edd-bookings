<?php

namespace Aventura\Edd\Bookings\Controller;

use \Aventura\Edd\Bookings\CustomPostType;
use \Aventura\Edd\Bookings\Factory\ModelCptFactoryAbstract;
use \Aventura\Edd\Bookings\Plugin;

/**
 * Description of ModelCptControllerAbstract
 *
 * @author Miguel Muscat <miguelmuscat93@gmail.com>
 */
abstract class ModelCptControllerAbstract extends ControllerAbstract
{

    /**
     * The custom post type instance.
     * 
     * @var CustomPostType
     */
    protected $_cpt;

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
        parent::__construct($plugin);
        $this->setFactory($factory);
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
     * Gets the custom post type instance.
     * 
     * @return CustomPostType The custom post type instance.
     */
    public function getPostType()
    {
        if (is_null($this->_cpt)) {
            $this->_cpt = $this->getFactory()->createCpt();
        }
        return $this->_cpt;
    }

    /**
     * Gets all meta fields for a particular object.
     * 
     * @param string|integer $id The Id of the object.
     * @return array The meta fields.
     */
    public function getMeta($id)
    {
        // Get all custom meta fields
        return array_map(function($item) {
            return $item[0];
        }, \get_post_custom($id));
    }

    /**
     * Gets a single object by ID.
     * 
     * @param integer $id The ID of the object to retrieve.
     */
    abstract public function get($id);

    /**
     * Creates and saves a new object into the database.
     * 
     * @param array $data Optional array of data. Default: array()
     * @param boolean $wp_error Whether to allow return of WP_Error on failure. Default: false
     * @return integer The post ID on success. The value 0 or WP_Error on failure.
     */
    public function insert(array $data = array(), $wp_error = false)
    {
        // Backup post data and unset it
        $postData = $_POST;
        unset($_POST);
        // Insert using given data
        $insertedId = \wp_insert_post($data, $wp_error);
        // Restore POST data
        $_POST = $postData;
        // Return inserted id
        return $insertedId;
    }
    
    /**
     * Saves an object's meta data into the database.
     * 
     * @param string|integer The ID of the object.
     * @param array $data Optional array of data. Default: array()
     */
    abstract public function saveMeta($id, array $data = array());

    /**
     * Queries the objects in the database.
     * 
     * @param array $query Optional query array that defines what objects are retrieved. If an empty array is given, all
     *                     objects are returned.
     * @return array An array of objects that matched the query.
     */
    abstract public function query(array $query = array());

}
