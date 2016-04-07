<?php

namespace Aventura\Edd\Bookings\Dashboard\Widget;

use \Aventura\Edd\Bookings\Controller\ControllerAbstract;
use \Aventura\Edd\Bookings\Plugin;

/**
 * Base implementation for an object that can act as a dashboard widget.
 *
 * @author Miguel Muscat <miguelmuscat93@gmail.com>
 */
abstract class WidgetAbstract extends ControllerAbstract implements WidgetInterface
{
    
    /**
     * The widget ID.
     * 
     * @var string
     */
    protected $_id;
    
    /**
     * The widget name.
     * 
     * @var string
     */
    protected $_name;
    
    /**
     * The callback that renders the widget output.
     * 
     * @var callable
     */
    protected $_outputCallback;
    
    /**
     * The callback that renders the widget options.
     * 
     * @var callable
     */
    protected $_optionsCallback;

    /**
     * Constructs a new instance.
     * 
     * @param Plugin $plugin The parent plugin instance.
     * @param string $id The widget ID.
     * @param string $name The widget name.
     */
    public function __construct(Plugin $plugin, $id, $name)
    {
        parent::__construct($plugin);
        $this->setId($id)
                ->setName($name)
                ->setOutputCallback(array($this, 'renderOutput'))
                ->setOptionsCallback(array($this, 'renderOptions'));
    }

    /**
     * Gets the widget ID.
     * 
     * @return string
     */
    public function getId()
    {
        return $this->_id;
    }

    /**
     * Gets the widget name.
     * 
     * @return string
     */
    public function getName()
    {
        return $this->_name;
    }

    /**
     * Gets the callback that renders the output.
     * 
     * @return callable.
     */
    public function getOutputCallback()
    {
        return $this->_outputCallback;
    }

    /**
     * Gets the callback that renders the options.
     * 
     * @return callable.
     */
    public function getOptionsCallback()
    {
        return $this->_optionsCallback;
    }

    /**
     * Sets the widget ID.
     * 
     * @param string $id The widget ID.
     * @return WidgetAbstract This instance.
     */
    public function setId($id)
    {
        $this->_id = $id;
        return $this;
    }

    /**
     * Sets the widget name.
     * 
     * @param string $name The widget name.
     * @return WidgetAbstract This instance.
     */
    public function setName($name)
    {
        $this->_name = $name;
        return $this;
    }

    /**
     * Sets the callback that renders the output.
     * 
     * @param callable $outputCallback The callable that renders the output.
     * @return WidgetAbstract This instance.
     */
    public function setOutputCallback($outputCallback)
    {
        $this->_outputCallback = $outputCallback;
        return $this;
    }

    /**
     * Sets the callback that renders the options.
     * 
     * @param callable $optionsCallback The callable that renders the output.
     * @return WidgetAbstract This instance.
     */
    public function setOptionsCallback($optionsCallback)
    {
        $this->_optionsCallback = $optionsCallback;
        return $this;
    }

    /**
     * Registers the dashboard widget.
     * 
     * @return WidgetAbstract This instance.
     */
    public function register()
    {
        wp_add_dashboard_widget($this->getId(), $this->getName(), $this->getOutputCallback(),
                $this->getOptionsCallback());
        return $this;
    }

    /**
     * Registers the WordPress hook.
     * 
     * @return WidgetAbstract This instance.
     */
    public function hook()
    {
        $this->getPlugin()->getHookManager()->addAction('wp_dashboard_setup', $this, 'register');
        return $this;
    }
    
    /**
     * Renders the widget output.
     * 
     * @return string The rendered output.
     */
    abstract public function renderOutput();
    
    /**
     * Renders the widget options.
     * 
     * @return string The rendered output.
     */
    abstract public function renderOptions();

}
