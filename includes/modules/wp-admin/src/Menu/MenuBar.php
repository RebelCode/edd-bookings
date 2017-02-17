<?php

namespace RebelCode\WordPress\Admin\Menu;

use \Dhii\App\AppInterface;
use \Psr\EventManager\EventManagerInterface;
use \RebelCode\EddBookings\System\Component\AbstractBaseComponent;

/**
 * The WordPress admin menu bar.
 *
 * @since [*next-version*]
 */
class MenuBar extends AbstractBaseComponent
{
    /**
     * The event manager.
     *
     * @since [*next-version*]
     *
     * @var EventManagerInterface
     */
    protected $eventManager;

    /**
     * The menus.
     *
     * @since [*next-version*]
     *
     * @var MenuInterface[]
     */
    protected $menus;

    /**
     * Constructor.
     *
     * @since [*next-version*]
     *
     * @param AppInterface $app The parent app.
     * @param EventManagerInterface $eventManager The event manager.
     */
    public function __construct(AppInterface $app, EventManagerInterface $eventManager)
    {
        parent::__construct($app);

        $this->setEventManager($eventManager);
        $this->menus = array();
    }

    /**
     * {@inheritdoc}
     *
     * @since [*next-version*]
     */
    public function onAppReady()
    {
        $this->getEventManager()->attach('admin_menu', array($this, 'registerMenus'));
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
     * Gets the menus to be registered.
     *
     * @since [*next-version*]
     *
     * @return MenuInterface[]
     */
    public function getMenus()
    {
        return $this->menus;
    }

    /**
     * Adds a menu to be registered with WordPress.
     *
     * @since [*next-version*]
     *
     * @param MenuInterface $menu The menu instance.
     *
     * @return $this This instance.
     */
    public function addMenu(MenuInterface $menu)
    {
        $this->menus[$menu->getId()] = $menu;

        return $this;
    }

    /**
     * Registers all the menus.
     *
     * @since [*next-version*]
     */
    public function registerMenus()
    {
        foreach ($this->getMenus() as $menu) {
            $menu->register();
        }

        return $this;
    }
}
