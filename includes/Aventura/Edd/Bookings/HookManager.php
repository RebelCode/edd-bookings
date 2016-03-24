<?php

namespace Aventura\Edd\Bookings;

/**
 * Manages WordPress hooks.
 * 
 * Adapted from Tom MacFarlin's version of the Loader class.
 */
class HookManager
{

    /**
     * The array of actions registered with WordPress.
     *
     * @var array $actions The actions registered with WordPress to fire when the plugin loads.
     */
    protected $_actions;

    /**
     * The array of filters registered with WordPress.
     *
     * @var array $filters The filters registered with WordPress to fire when the plugin loads.
     */
    protected $_filters;

    /**
     * Constructs a new instance.
     */
    public function __construct()
    {
        $this->_actions = array();
        $this->_filters = array();
    }
    
    /**
     * Add a new action to the collection to be registered with WordPress.
     *
     * @var      string               $hook             The name of the WordPress action that is being registered.
     * @var      object               $component        A reference to the instance of the object on which the action is defined.
     * @var      string               $callback         The name of the function definition on the $component.
     * @var      int      Optional    $priority         The priority at which the function should be fired.
     * @var      int      Optional    $accepted_args    The number of arguments that should be passed to the $callback.
     * @return HookManager This instance.
     */
    public function addAction($hook, $component, $callback, $priority = 10, $accepted_args = 1)
    {
        $this->_actions = $this->add($this->_actions, $hook, $component, $callback, $priority, $accepted_args);
        return $this;
    }

    /**
     * Add a new filter to the collection to be registered with WordPress.
     *
     * @since    1.0.0
     * @var      string               $hook             The name of the WordPress filter that is being registered.
     * @var      object               $component        A reference to the instance of the object on which the filter is defined.
     * @var      string               $callback         The name of the function definition on the $component.
     * @var      int      Optional    $priority         The priority at which the function should be fired.
     * @var      int      Optional    $accepted_args    The number of arguments that should be passed to the $callback.
     * @return HookManager This instance.
     */
    public function addFilter($hook, $component, $callback, $priority = 10, $accepted_args = 1)
    {
        $this->_filters = $this->add($this->_filters, $hook, $component, $callback, $priority, $accepted_args);
        return $this;
    }

    /**
     * A utility function that is used to register the actions and hooks into a single
     * collection.
     *
     * @var      array                $hooks            The collection of hooks that is being registered (that is, actions or filters).
     * @var      string               $hook             The name of the WordPress filter that is being registered.
     * @var      object               $component        A reference to the instance of the object on which the filter is defined.
     * @var      string               $callback         The name of the function definition on the $component.
     * @var      int      Optional    $priority         The priority at which the function should be fired.
     * @var      int      Optional    $accepted_args    The number of arguments that should be passed to the $callback.
     * @return   type                                   The collection of actions and filters registered with WordPress.
     */
    public function add($hooks, $hook, $component, $callback, $priority, $accepted_args)
    {

        $hooks[] = array(
            'hook' => $hook,
            'component' => $component,
            'callback' => $callback,
            'priority' => $priority,
            'accepted_args' => $accepted_args
        );

        return $hooks;
    }

    /**
     * Register the filters and actions with WordPress.
     */
    public function registerHooks()
    {
        foreach ($this->_filters as $hook) {
            $callback = static::getNormalizedCallable($hook);
            \add_filter($hook['hook'], $callback, $hook['priority'], $hook['accepted_args']);
        }
        foreach ($this->_actions as $hook) {
            $callback = static::getNormalizedCallable($hook);
            \add_action($hook['hook'], $callback, $hook['priority'], $hook['accepted_args']);
        }
    }
    
    /**
     * Gets the normalized callable for the given hook.
     * 
     * @param array $hook The hook array.
     * @return mixed A string for a function, an array for a callable method or null on failure.
     */
    public static function getNormalizedCallable($hook, $default = 'nonExistingFunction') 
    {
        if (!array_key_exists('component', $hook) || !array_key_exists('callback', $hook)) {
            return $default;
        }
        return is_null($hook['component'])
                    ? $hook['callback']
                    : array($hook['component'], $hook['callback']);
    }

}
