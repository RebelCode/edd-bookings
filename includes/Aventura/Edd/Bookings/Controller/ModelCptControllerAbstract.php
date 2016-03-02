<?php

namespace Aventura\Edd\Bookings\Controller;

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
     * Constructs a new instance.
     * 
     * @param Plugin $plugin The parent plugin instance.
     */
    public function __construct(Plugin $plugin, ModelCptFactoryAbstract $factory)
    {
        parent::__construct($plugin, $factory);
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

}
