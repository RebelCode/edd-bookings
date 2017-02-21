<?php

namespace RebelCode\EddBookings\Admin\Debug;

use \Dhii\App\AppInterface;
use \Dhii\Di\FactoryInterface;
use \Psr\EventManager\EventManagerInterface;
use \RebelCode\EddBookings\Block\Html\CompositeTag;
use \RebelCode\EddBookings\Block\Html\DivTag;
use \RebelCode\EddBookings\Block\Html\ListBlock;
use \RebelCode\EddBookings\Block\Html\RegularTag;
use \RebelCode\EddBookings\System\Component\AbstractBaseComponent;
use \RebelCode\WordPress\Admin\Menu\MenuBar;
/**
 * Description of DebugPage
 *
 * @since [*next-version*]
 */
class DebugPage extends AbstractBaseComponent
{
    /**
     * The WordPress menu.
     *
     * @var type
     */
    protected $wpAdminMenuBar;

    /**
     * The event manager.
     *
     * @since [*next-version*]
     *
     * @var EventManagerInterface
     */
    protected $eventManager;

    /**
     * The factory - used to create menus.
     *
     * @since [*next-version*]
     *
     * @var FactoryInterface
     */
    protected $factory;

    public function __construct(
        AppInterface $app,
        MenuBar $wpAdminMenuBar,
        EventManagerInterface $eventManager,
        FactoryInterface $factory
    ) {
        parent::__construct($app);

        $this->setEventManager($eventManager)
            ->setWpAdminMenuBar($wpAdminMenuBar)
            ->setFactory($factory);
    }

    /**
     * Gets the WordPress admin menu bar.
     *
     * @since [*next-version*]
     *
     * @return MenuBar The WordPress admin menu bar instance.
     */
    public function getWpAdminMenuBar()
    {
        return $this->wpAdminMenuBar;
    }

    /**
     * Sets the WordPress admin menu bar.
     *
     * @since [*next-version*]
     *
     * @param MenuBar $wpAdminMenuBar The new WordPress admin menu bar instance.
     *
     * @return $this This instance.
     */
    public function setWpAdminMenuBar($wpAdminMenuBar)
    {
        $this->wpAdminMenuBar = $wpAdminMenuBar;

        return $this;
    }

    /**
     * Gets the event manager.
     *
     * @since [*next-version*]
     *
     * @return EventManagerInterface The event manager instance.
     */
    public function getEventManager()
    {
        return $this->eventManager;
    }

    /**
     * Sets the event manager.
     *
     * @since [*next-version*]
     *
     * @param EventManagerInterface $eventManager The event manager instance.
     *
     * @return $this This instance.
     */
    public function setEventManager($eventManager)
    {
        $this->eventManager = $eventManager;

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
     * Gets the page content.
     *
     * @since [*next-version*]
     *
     * @return DivTag
     */
    public function getContent()
    {
        $modulesLoaded = eddBkContainer()->get('module_loader')->getLoadedModules();
        $moduleNames   = array_map(function($module) {
            return $module->getName();
        }, $modulesLoaded);

        $modulesList   = new ListBlock($moduleNames, array(
            'style' => 'list-style: disc inside'
        ));

        return new CompositeTag('div', array('class' => 'wrap'), array(
            new RegularTag('h2', array(), __('Loaded Modules', 'eddbk')),
            $modulesList
        ));
    }

    /**
     * {@inheritdoc}
     *
     * @since [*next-version*]
     */
    public function onAppReady()
    {
        $page = $this->getFactory()->make('admin_page', array(
            'title'      => 'Debuggsaru',
            'capability' => 'manage_options',
            'content'    => $this->getContent()
        ));
        $menu = $this->getFactory()->make('admin_submenu', array(
            'menu_id' => 'edit.php?post_type=edd_booking',
            'id'      => 'debugging',
            'label'   => __('Debugging', 'eddbk'),
            'page'    => $page
        ));

        $this->getWpAdminMenuBar()->addMenu($menu);
    }
}
