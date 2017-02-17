<?php

namespace RebelCode\EddBookings\System\ModuleLoader;

use \Composer\Autoload\ClassLoader;
use \Dhii\App\AppInterface;
use \Dhii\Di\FactoryInterface;
use \Dhii\Di\ServiceProvider;
use \Interop\Container\ContainerInterface;
use \Interop\Container\ServiceProvider as ServiceProviderInterface;
use \InvalidArgumentException;
use \RebelCode\EddBookings\System\Component\AbstractBaseComponent;
use \RebelCode\EddBookings\System\Component\ComponentInterface;
use \RebelCode\EddBookings\System\Module\ModuleInterface;

/**
 * Loads modules using a JSON manifest file.
 *
 * @since [*next-version*]
 */
class JsonModuleLoader extends AbstractBaseComponent implements
    ComponentInterface,
    ModuleLoaderInterface
{
    /**
     * The file name of a module's manifest file.
     *
     * @since [*next-version*]
     */
    const MODULE_MANIFEST_FILE = 'manifest.json';

    /**
     * The file name of a module's main file.
     *
     * @since [*next-version*]
     */
    const MODULE_MAIN_FILE = 'module.php';

    /**
     * The file name of a module's services file.
     *
     * @since [*next-version*]
     */
    const SERVICE_PROVIDER_FILE = 'services.php';

    /**
     * The file name of a module's config file.
     *
     * @since [*next-version*]
     */
    const CONFIG_FILE = 'config.php';

    /**
     * The container - used to fetch services.
     *
     * @since [*next-version*]
     *
     * @var ContainerInterface
     */
    protected $container;

    /**
     * The factory - used to create module instances.
     *
     * @since [*next-version*]
     *
     * @var FactoryInterface
     */
    protected $factory;

    /**
     * The autoloader - used to register module autoloading.
     *
     * @since [*next-version*]
     *
     * @var ClassLoader
     */
    protected $autoloader;

    /**
     * The loaded modules.
     *
     * @since [*next-version*]
     *
     * @var ModuleInterface[]
     */
    protected $loadedModules;

    /**
     * A map of unresolved dependencies at any given point in time.
     *
     * @since [*next-version*]
     *
     * @var array
     */
    protected $dependencies;

    /**
     * Reverse de-map of module dependencies, used to load dependents of modules.
     *
     * @since [*next-version*]
     *
     * @var array
     */
    protected $dependents;

    /**
     * A stack that contains the current load chain.
     *
     * @since [*next-version*]
     *
     * @var array
     */
    protected $loadChain;

    /**
     * Constructor.
     *
     * @since [*next-version*]
     *
     * @param AppInterface $plugin The app to which module components will be registered.
     * @param ContainerInterface $container The container from which component services can be fetched.
     * @param FactoryInterface $factory The factory to use to create module instances.
     * @param ClassLoader $autoloader The autoloader to which module autoload rules will be added.
     */
    public function __construct(
        AppInterface $plugin,
        ContainerInterface $container,
        FactoryInterface $factory,
        ClassLoader $autoloader
    ) {
        parent::__construct($plugin);

        $this->setContainer($container)
            ->setFactory($factory)
            ->setAutoloader($autoloader);

        $this->_setLoadedModules(array());

        $this->dependencies = array();
        $this->dependents   = array();
        $this->loadChain    = array();
    }

    /**
     * Gets the container instance.
     *
     * @since [*next-version*]
     *
     * @return ContainerInterface The container instance.
     */
    public function getContainer()
    {
        return $this->container;
    }

    /**
     * Sets the container instance.
     *
     * @since [*next-version*]
     *
     * @param ContainerInterface $container The new container instance.
     *
     * @return $this This instance.
     */
    public function setContainer(ContainerInterface $container)
    {
        $this->container = $container;

        return $this;
    }

    /**
     * Gets the factory instance.
     *
     * @since [*next-version*]
     *
     * @return FactoryInterface The factory instance.
     */
    public function getFactory()
    {
        return $this->factory;
    }

    /**
     * Sets the factory instance.
     *
     * @since [*next-version*]
     *
     * @param FactoryInterface $factory The new factory instance.
     *
     * @return $this This instance.
     */
    public function setFactory(FactoryInterface $factory)
    {
        $this->factory = $factory;

        return $this;
    }

    /**
     * Retrieves the autoloader.
     *
     * @since [*next-version*]
     *
     * @return ClassLoader
     */
    public function getAutoloader()
    {
       return $this->autoloader;
    }

    /**
     * Sets the autoloader.
     *
     * @since [*next-version*]
     *
     * @param ClassLoader $autoloader The new autoloader instance.
     *
     * @return $this This instance.
     */
    public function setAutoloader(ClassLoader $autoloader)
    {
       $this->autoloader = $autoloader;

       return $this;
    }

    /**
     * {@inheritdoc}
     *
     * @since [*next-version*]
     */
    public function loadModule($path)
    {
        $module = $this->_getModuleAtPath($path);

        if (!is_null($module)) {
            $this->_maybeLoadModule($module);
        }

        return $module;
    }

    /**
     * {@inheritdoc}
     *
     * @since [*next-version*]
     */
    public function getLoadedModules()
    {
        return $this->loadedModules;
    }

    /**
     * Gets the module instance for the module found at a specific path.
     *
     * @since [*next-version*]
     *
     * @param string $path The path.
     *
     * @return ModuleInterface|null The module instance or null if no module was found.
     */
    protected function _getModuleAtPath($path)
    {
        $filepath = $path . DIRECTORY_SEPARATOR . static::MODULE_MANIFEST_FILE;
        $data     = $this->_readJsonFile($filepath);

        return is_null($data)
            ? null
            : $this->_createModule($data);
    }

    /**
     * Creates a module instance.
     *
     * @since [*next-version*]
     *
     * @param array $data An associative array containing the module data.
     *
     * @return ModuleInterface The created module instance.
     */
    protected function _createModule(array $data)
    {
        return $this->getFactory()->make('module', $data);
    }

    /**
     * Reads and parses a module JSON file.
     *
     * @since [*next-version*]
     *
     * @param string $filepath The path to the module JSON file.
     *
     * @return array|null An associative array containing the parsed data or null if the JSON
     *                    file does not exist or is not readable.
     */
    protected function _readJsonFile($filepath)
    {
        if (!is_readable($filepath)) {
            return null; // @todo Throw exception
        }

        $raw       = file_get_contents($filepath);
        $jsonData  = json_decode($raw, true);
        $directory = dirname($filepath);

        $baseData  = array(
            'id'        => basename($directory),
            'directory' => $directory,
            'file_path' => $directory . DIRECTORY_SEPARATOR . static::MODULE_MAIN_FILE
        );

        $defaults = $this->_getModuleDataDefaults();
        $data     = array_merge_recursive($baseData, $jsonData, $defaults);

        return $data;
    }

    /**
     * Gets the default values for non-generated module data.
     *
     * @since [*next-version*]
     *
     * @return array An associative array of data.
     */
    protected function _getModuleDataDefaults()
    {
        return array(
            'autoload'   => array(
                'psr-4' => array(),
                'psr-0' => array()
            ),
            'components' => array(),
            'requires'   => array()
        );
    }

    /**
     * Attempts to load the module, stopping if the module is already loaded or it has
     * dependencies that are not yet loaded.
     *
     * @since [*next-version*]
     *
     * @param ModuleInterface $module The module to attempt to load.
     *
     * @return boolean True if the module was loaded, false if not.
     */
    protected function _maybeLoadModule(ModuleInterface $module)
    {
        if ($this->_isModuleLoaded($module)) {
            return true;
        }

        $dependencies = $this->_getRemainingDependencies($module);

        if (count($dependencies) === 0) {
            $this->_registerModuleAutoload($module)
                ->_registerModuleConfig($module)
                ->_registerModuleServices($module)
                ->_registerModuleComponents($module)
                ->_setModuleLoaded($module);

            $this->_loadModuleDependents($module);

            return true;
        }

        $this->_setModuleToLoadAfter($module, $dependencies);

        return false;
    }

    /**
     * Gets the module dependencies that are not yet loaded.
     *
     * @since [*next-version*]
     *
     * @param ModuleInterface $module The module.
     *
     * @return array An array of module IDs.
     */
    protected function _getRemainingDependencies(ModuleInterface $module)
    {
        $dependencies = $this->_getModuleDependencyCache($module);
        $remaining    = array();

        foreach ($dependencies as $_dependency) {
            if (!$this->_isModuleLoaded($_dependency) && !in_array($_dependency, $this->loadChain)) {
                $remaining[] = $_dependency;
            }
        }

        $this->_setModuleDependencyCache($module, $remaining);

        return $remaining;
    }

    /**
     * Attempts to load a module's dependents.
     *
     * @since [*next-version*]
     *
     * @param ModuleInterface $module The module whose dependents are to be loaded.
     *
     * @return $this This instance.
     */
    protected function _loadModuleDependents(ModuleInterface $module)
    {
        array_push($this->loadChain, $module->getId());

        $dependents = $this->_getModuleDependents($module);

        foreach ($dependents as $_dependent) {
            $this->_maybeLoadModule($_dependent);
        }

        array_pop($this->loadChain);

        return $this;
    }

    /**
     * Gets the module dependencies from cache.
     *
     * @since [*next-version*]
     *
     * @param ModuleInterface $module The module instance.
     *
     * @return array
     */
    protected function _getModuleDependencyCache(ModuleInterface $module)
    {
        if (!$this->_hasModuleDependencyCache($module)) {
            $this->_setModuleDependencyCache($module, $module->getData('requires', array()));
        }

        return $this->dependencies[$module->getId()];
    }

    /**
     * Inserts a module's dependencies into cache.
     *
     * @since [*next-version*]
     *
     * @param ModuleInterface $module The module.
     * @param array $dependencies The dependencies.
     *
     * @return $this This instance.
     */
    protected function _setModuleDependencyCache(ModuleInterface $module, array $dependencies)
    {
        $this->dependencies[$module->getId()] = $dependencies;

        return $this;
    }

    /**
     * Checks if a module has its dependencies in cache.
     *
     * @since [*next-version*]
     *
     * @param ModuleInterface $module The module.
     *
     * @return bool True if the module has dependencies in cache, false if not.
     */
    protected function _hasModuleDependencyCache(ModuleInterface $module)
    {
        return isset($this->dependencies[$module->getId()]);
    }

    /**
     * Checks if a module with the given ID has been loaded.
     *
     * @since [*next-version*]
     *
     * @param ModuleInterface|string $arg The module instance or the ID of the module.
     *
     * @return bool True if the module has been loaded, false if not.
     */
    protected function _isModuleLoaded($arg)
    {
        $id = ($arg instanceof ModuleInterface)
            ? $arg->getId()
            : $arg;

        return isset($this->loadedModules[$id]);
    }

    /**
     * Sets a module as loaded.
     *
     * @since [*next-version*]
     *
     * @param ModuleInterface $module The module to mark as loaded.
     *
     * @return $this This instance.
     */
    protected function _setModuleLoaded(ModuleInterface $module)
    {
        $id                       = $module->getId();
        $this->loadedModules[$id] = $module;

        return $this;
    }

    /**
     * Sets which modules have been loaded.
     *
     * @since [*next-version*]
     *
     * @param ModuleInterface[] $loadedModules An array of module instances mapped by their IDs.
     *
     * @return $this This instance.
     */
    protected function _setLoadedModules(array $loadedModules)
    {
        $this->loadedModules = $loadedModules;

        return $this;
    }

    /**
     * Gets the modules that need to be loaded after a specific module.
     *
     * @since [*next-version*]
     *
     * @param ModuleInterface|string $arg The module instance or the ID of the module.
     *
     * @return array An array of module IDs to be loaded after the given module.
     */
    protected function _getModuleDependents($arg)
    {
        $id = ($arg instanceof ModuleInterface)
            ? $arg->getId()
            : $arg;

        return isset($this->dependents[$id])
            ? $this->dependents[$id]
            : array();
    }

    /**
     * Sets a module to be the dependent of another.
     *
     * @param string $dependencyId The dependency module ID.
     * @param ModuleInterface $module The dependent module.
     *
     * @return $this This instance.
     */
    protected function _setModuleDependent($dependencyId, ModuleInterface $module)
    {
        if (!isset($this->dependents[$dependencyId])) {
            $this->dependents[$dependencyId] = array();
        }

        $this->dependents[$dependencyId][$module->getId()] = $module;

        return $this;
    }

    /**
     * Marks a module to be loaded after a given set of dependencies.
     *
     * @since [*next-version*]
     *
     * @param ModuleInterface $module The module.
     * @param array $dependencies An array of dependency module IDs.
     *
     * @return $this This instance.
     */
    protected function _setModuleToLoadAfter(ModuleInterface $module, array $dependencies)
    {
        foreach ($dependencies as $_dependency) {
            $this->_setModuleDependent($_dependency, $module);
        }

        return $this;
    }

    /**
     * Registers an autoload rule for a module.
     *
     * @since [*next-version*]
     *
     * @param ModuleInterface $module The module instance.
     *
     * @return $this This instance.
     */
    protected function _registerModuleAutoload(ModuleInterface $module)
    {
        $moduleAutoload = $module->getData('autoload', array());

        $this->_registerAutoloadPsr4($module->getData('directory'), $moduleAutoload['psr-4']);
        $this->_registerAutoloadPsr0($module->getData('directory'), $moduleAutoload['psr-0']);

        return $this;
    }

    /**
     * Registers PSR-4 style autoloading.
     *
     * @param string $basePath The base path.
     * @param array  $psr4     The PSR-4 autoload rules.
     *
     * @return $this This instance.
     */
    protected function _registerAutoloadPsr4($basePath, array $psr4)
    {
        $autoloader = $this->getAutoloader();

        foreach ($psr4 as $_nsPrefix => $_path) {
            $autoloader->addPsr4($_nsPrefix, $basePath . DIRECTORY_SEPARATOR . $_path);
        }


        return $this;
    }

    /**
     * Registers PSR-0 style autoloading.
     *
     * @param string $basePath The base path.
     * @param array  $psr0     The PSR-0 autoload rules.
     *
     * @return $this This instance.
     */
    protected function _registerAutoloadPsr0($basePath, $psr0)
    {
        $autoloader = $this->getAutoloader();

        foreach ($psr0 as $_nsPrefix => $_autoloadPaths) {
            foreach ((array) $_autoloadPaths as $_path) {
                $autoloader->add($_nsPrefix, $basePath . DIRECTORY_SEPARATOR . $_path);
            }
        }

        return $this;
    }

    /**
     * Loads a module's services file.
     *
     * @since [*next-version*]
     *
     * @param ModuleInterface $module The module.
     *
     * @return array The read services array or service provider.
     */
    protected function _loadModuleServicesFile(ModuleInterface $module)
    {
        $file = $module->getDirectory() . DIRECTORY_SEPARATOR . static::SERVICE_PROVIDER_FILE;

        if (!is_readable($file)) {
            return array();
        }

        return require $file;
    }

    /**
     * Registers a module's services.
     *
     * @since [*next-version*]
     *
     * @param ModuleInterface $module The module.
     *
     * @return $this This instance.
     *
     * @throws InvalidArgumentException If the module services file did not return an array
     *                                  or service provider instance.
     */
    protected function _registerModuleServices(ModuleInterface $module)
    {
        $services = $this->_loadModuleServicesFile($module);

        $provider = is_array($services)
            ? new ServiceProvider($services)
            : $services;

        if (!$provider instanceof ServiceProviderInterface) {
            throw new InvalidArgumentException(
                sprintf('Invalid service provider given by module "%s"', $module->getId())
            );
        }

        $this->getApp()->getContainer()->register($provider);

        return $this;
    }

    /**
     * Registers the services delcared as components in a module's manifest.
     *
     * @since [*next-version*]
     *
     * @param ModuleInterface $module The module.
     *
     * @return $this This instance.
     */
    protected function _registerModuleComponents(ModuleInterface $module)
    {
        $components = $module->getData('components', array());

        foreach ($components as $_componentId => $_serviceId) {
            $component = $this->getContainer()->get($_serviceId);
            $this->getApp()->registerComponent($component, $_componentId);
        }

        return $this;
    }

    /**
     * Loads a module's config file.
     *
     * @since [*next-version*]
     *
     * @param ModuleInterface $module The module.
     *
     * @return array The read config data.
     */
    protected function _loadModuleConfigFile(ModuleInterface $module)
    {
        $file = $module->getDirectory() . DIRECTORY_SEPARATOR . static::CONFIG_FILE;

        if (!is_readable($file)) {
            return array();
        }

        return require $file;
    }

    /**
     *
     * @param ModuleInterface $module
     * @return $this
     */
    protected function _registerModuleConfig(ModuleInterface $module)
    {
        $config = $this->_loadModuleConfigFile($module);

        foreach ($config as $_key => $_value) {
            $module->setData($_key, $_value);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     *
     * @since [*next-version*]
     */
    public function onAppReady()
    {
    }
}
