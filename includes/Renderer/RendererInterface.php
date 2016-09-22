<?php

namespace Aventura\Edd\Bookings\Renderer;

use \Aventura\Edd\Bookings\Plugin;

/**
 * An object that can render another object into HTML.
 * 
 * @author Miguel Muscat <miguelmuscat93@gmail.com>
 */
interface RendererInterface
{
    
    /**
     * Gets the object being rendered.
     * 
     * @return mixed
     */
    public function getObject();
    
    /**
     * Renders the objcet.
     * 
     * @param array $data Optional array of data. Default: array()
     */
    public function render(array $data = array());

}
