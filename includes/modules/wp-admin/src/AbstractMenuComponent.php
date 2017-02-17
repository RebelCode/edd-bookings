<?php

namespace RebelCode\WordPress\Admin;

use \Dhii\App\AppInterface;
use \Dhii\Di\FactoryInterface;
use \Psr\EventManager\EventManagerInterface;
use \RebelCode\EddBookings\Block\BlockInterface;
use \RebelCode\EddBookings\System\Component\AbstractBaseComponent;
use \RebelCode\WordPress\Admin\Menu\MenuBar;

/**
 * Basic functionality for an admin menu component.
 *
 * @since [*next-version*]
 */
abstract class AbstractMenuComponent extends AbstractBaseComponent
{
    /**
     * The WordPress menu bar.
     *
     * @var type
     */
    protected $menuBar;

    /**
     * The event manager.
     *
     * @since [*next-version*]
     *
     * @var EventManagerInterface
     */
    protected $eventManager;
    /**
     * Constructor.
     *
     * @since [*next-version*]
     *
     * @param AppInterface $app The parent app instance.
     * @param MenuBar $adminMenuBar The WP admin menu bar.
     * @param EventManagerInterface $eventManager The event manager.
     */
    public function __construct(
        AppInterface $app,
        MenuBar $adminMenuBar,
        EventManagerInterface $eventManager
    ) {
        parent::__construct($app);

        $this->setEventManager($eventManager)
            ->setMenuBar($adminMenuBar);
    }

    /**
     * Gets the admin menu instance.
     *
     * @since [*next-version*]
     *
     * @return Menu\MenuInterface The menu instance.
     */
    abstract public function getMenu();

    /**
     * {@inheritdoc}
     *
     * @since [*next-version*]
     */
    public function onAppReady()
    {
        $this->getMenuBar()->addMenu($this->getMenu());
    }

    /**
     * Gets the WordPress admin menu bar.
     *
     * @since [*next-version*]
     *
     * @return MenuBar The WordPress admin menu bar instance.
     */
    public function getMenuBar()
    {
        return $this->menuBar;
    }

    /**
     * Sets the WordPress admin menu bar.
     *
     * @since [*next-version*]
     *
     * @param MenuBar $menuBar The new WordPress admin menu bar instance.
     *
     * @return $this This instance.
     */
    public function setMenuBar($menuBar)
    {
        $this->menuBar = $menuBar;

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
}
