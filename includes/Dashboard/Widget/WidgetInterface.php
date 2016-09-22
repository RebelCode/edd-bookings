<?php

namespace Aventura\Edd\Bookings\Dashboard\Widget;

/**
 * Generic definition of an object that can act as a dashboard widget.
 * 
 * @author Miguel Muscat <miguelmuscat93@gmail.com>
 */
interface WidgetInterface
{
    
    /**
     * Gets the widget ID.
     * 
     * @return integer
     */
    public function getId();
    
    /**
     * Gets the widget name.
     * 
     * @return string
     */
    public function getName();
    
    /**
     * Gets the callback that outputs the widget content.
     * 
     * @return callable
     */
    public function getOutputCallback();
    
    /**
     * Gets the callback that outputs the widget options.
     * 
     * @reurn callable
     */
    public function getOptionsCallback();

}
