<?php

namespace Aventura\Edd\Bookings\Factory;


/**
 * Generic definition of a factory.
 * 
 * @author Miguel Muscat <miguelmuscat93@gmail.com>
 */
interface FactoryInterface
{
    
    /**
     * Creates a new instance.
     * 
     * @param array $data The data to use for instantiation.
     * @return mixed The created instance.
     */
    public function create(array $data);
    
    /**
     * Gets the parent plugin instance.
     * 
     * @return Plugin
     */
    public function getPlugin();

}
