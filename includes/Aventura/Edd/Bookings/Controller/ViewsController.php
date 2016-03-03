<?php

namespace Aventura\Edd\Bookings\Controller;

use \Aventura\Edd\Bookings\Plugin;

/**
 * Views controller.
 *
 * @author Miguel Muscat <miguelmuscat93@gmail.com>
 */
class ViewsController extends ControllerAbstract
{

    /**
     * The views directory.
     * 
     * @var string
     */
    protected $_viewsDir;

    public function __construct(Plugin $plugin, $viewsDir)
    {
        parent::__construct($plugin);
        $this->setViewsDir($viewsDir);
    }

    /**
     * Gets the views directory.
     * 
     * @return string
     */
    public function getViewsDir()
    {
        return $this->_viewsDir;
    }

    /**
     * Sets the views directory.
     * 
     * @param string $viewsDir The views directory.
     * @return ViewsController This instance.
     */
    public function setViewsDir($viewsDir)
    {
        $this->_viewsDir = $viewsDir;
        return $this;
    }

    /**
     * Registers the WordPress hooks.
     * 
     * @return ViewsController This instance.
     */
    public function hook()
    {
        return $this;
    }

}
