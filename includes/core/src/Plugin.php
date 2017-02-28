<?php

namespace RebelCode\EddBookings;

use \Dhii\App\ComponentInterface as BaseComponentInterface;
use \Dhii\Di\Container;
use \Dhii\Di\FactoryInterface;
use \Interop\Container\ContainerInterface;
use \Interop\Container\Exception\ContainerException;
use \Interop\Container\Exception\NotFoundException;
use \RebelCode\EddBookings\System\AbstractPlugin;
use \RebelCode\EddBookings\System\Component\ComponentInterface;
use \RebelCode\EddBookings\System\LoopMachine;
use \RebelCode\EddBookings\System\PluginInterface;
use \SplFileInfo;
use \SplObserver;
use \SplSubject;

/**
 * Plugin hub class.
 *
 * @since [*next-version*]
 */
class Plugin extends AbstractPlugin implements PluginInterface, SplObserver
{
    /**
     * Constructor.
     *
     * @since [*next-version*]
     *
     * @param ContainerInterface $container The DI Container.
     */
    public function __construct(ContainerInterface $container, FactoryInterface $factory)
    {
        $this->_setContainer($container)
            ->_setFactory($factory)
            ->_resetComponents();
    }

    /**
     * {@inheritdoc}
     *
     * @since [*next-version*]
     */
    protected function _getContainer()
    {
        return $this->container;
    }

    /**
     * {@inheritdoc}
     *
     * @since [*next-version*]
     */
    protected function _getFactory()
    {
        return $this->factory;
    }

    /**
     * Gets the DI container.
     *
     * @since [*next-version*]
     *
     * @return Container The DI container instance.
     */
    public function getContainer()
    {
        return $this->_getContainer();
    }

    /**
     * Gets the factory.
     *
     * @since [*next-version*]
     *
     * @return FactoryInterface The factory instance.
     */
    public function getFactory()
    {
        return $this->_getFactory();
    }

    /**
     * Retrieves all the components.
     *
     * @since [*next-version*]
     *
     * @return ComponentInterface[] An array of components mapped by their codes.
     */
    public function getComponents()
    {
        return $this->_getComponents();
    }

    /**
     * {@inheritdoc}
     *
     * @since [*next-version*]
     */
    public function hasComponent($code)
    {
        return $this->_hasComponent($code);
    }

    /**
     * {@inheritdoc}
     *
     * @since [*next-version*]
     *
     * @throws NotFoundException If no component exists with the given $code.
     * @throws ContainerException If an error occurred while creating the component instance.
     */
    public function getComponent($code)
    {
        return $this->_getComponent($code);
    }

    /**
     * {@inheritdoc}
     *
     * @since [*next-version*]
     */
    public function registerComponent(BaseComponentInterface $component, $code)
    {
        $this->_registerComponent($component, $code);

        return $this;
    }

    /**
     * {@inheritdoc}
     *
     * @since [*next-version*]
     */
    public function update(SplSubject $subject)
    {
        if (!($subject instanceof LoopMachine)) {
            return;
        }

        if ($subject->getState() !== LoopMachine::STATE_LOOP) {
            return;
        }

        if ($subject->getCurrent() instanceof SplFileInfo) {
            $this->loadModule($subject->getCurrent()->getPathName());
        }

        return;
    }

    /**
     * Loads a module at a specific path.
     *
     * @param string $filePath The full path to the module.json file.
     *
     * @return
     */
    public function loadModule($filePath)
    {
        $module = $this->di('module_loader')->loadModule($filePath);
        $module->load();
    }

    /**
     * {@inheritdoc}
     *
     * @since [*next-version*]
     *
     * @return $this This instance.
     */
    public function run()
    {
        $this->_loadModules();
        $this->_initComponents();

        return $this;
    }

    /**
     * Loads the modules.
     *
     * @since [*next-version*]
     *
     * @return $this This instance.
     */
    protected function _loadModules()
    {
        // Create module loop machine
        $loopMachine  = $this->factory('loop_machine');
        $loopMachine->attach($this);

        // Get the module finder
        // @todo Make Module Finder Interface
        $moduleFinder = $this->di('module_finder');

        // Loop machine iterates over found modules
        $loopMachine->process($moduleFinder);

        return $this;
    }

    /**
     * Initializes the components by triggering their {@see ComponentInterface::onAppReady()} method.
     *
     * @since [*next-version*]
     *
     * @return $this This instance.
     */
    protected function _initComponents()
    {
        foreach ($this->_getComponents() as $_code => $_component) {
            $this->_initComponent($_code, $_component);
        }

        return $this;
    }

    protected function _initComponent($code, ComponentInterface $component)
    {
        try {
            $component->onAppReady();
        } catch (\Exception $e) {
            throw new Exception(
                sprintf('Component %s triggered an exception', $code), 1, $e
            );
        }
    }
}
