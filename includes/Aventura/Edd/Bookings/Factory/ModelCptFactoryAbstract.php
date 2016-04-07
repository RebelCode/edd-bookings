<?php

namespace Aventura\Edd\Bookings\Factory;

use \Aventura\Edd\Bookings\CustomPostType;

/**
 * Description of ModelCptFactoryAbstract
 *
 * @author Miguel Muscat <miguelmuscat93@gmail.com>
 */
abstract class ModelCptFactoryAbstract extends FactoryAbstract
{
    
    /**
     * Creates the custom post type.
     * 
     * @param array $data Optional array of data. Default: array()
     * @return CustomPostType The created instance.
     */
    abstract public function createCpt(array $data = array());

}
