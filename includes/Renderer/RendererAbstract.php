<?php

namespace Aventura\Edd\Bookings\Renderer;

/**
 * An object that can render another object into HTML.
 *
 * @author Miguel Muscat <miguelmuscat93@gmail.com>
 */
abstract class RendererAbstract implements RendererInterface
{
    
    /**
     * The object to render.
     * 
     * @var mixed
     */
    protected $_object;
    
    /**
     * Constructs a new instance.
     * 
     * @param mixed $object The object to render.
     */
    public function __construct($object)
    {
        $this->setObject($object);
    }

    /**
     * Gets the object to render.
     * 
     * @return mixed The object to render.
     */
    public function getObject()
    {
        return $this->_object;
    }
    
    /**
     * Sets the object to render.
     * 
     * @param mixed $object The object to render.
     * @return RendererAbstract This instance.
     */
    public function setObject($object)
    {
        $this->_object = $object;
        return $this;
    }

}
