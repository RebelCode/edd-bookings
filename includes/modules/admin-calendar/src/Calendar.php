<?php

namespace RebelCode\EddBookings\Admin\Calendar;

use \Dhii\App\AppInterface;
use \Psr\EventManager\EventManagerInterface;
use \RebelCode\WordPress\Admin\AbstractMenuComponent;
use \RebelCode\WordPress\Admin\Menu\MenuBar;
use \RebelCode\WordPress\Admin\Menu\MenuInterface;

/**
 * Component for the admin calendar.
 *
 * @since [*next-version*]
 */
class Calendar extends AbstractMenuComponent
{
    /**
     * The menu.
     *
     * @since [*next-version*]
     *
     * @var MenuInterface
     */
    protected $menu;

    /**
     * {@inheritdoc}
     *
     * @since [*next-version*]
     *
     * @param MenuInterface $menu The menu isntance.
     */
    public function __construct(
        AppInterface $app,
        MenuBar $adminMenuBar,
        EventManagerInterface $eventManager,
        MenuInterface $menu
    ) {
        parent::__construct($app, $adminMenuBar, $eventManager, $menu);

        $this->setMenu($menu);
    }

    /**
     * {@inheritdoc}
     *
     * @since [*next-version*]
     */
    public function getMenu()
    {
        return $this->menu;
    }

    /**
     * Sets the menu.
     *
     * @param MenuInterface $menu The menu instance.
     *
     * @return $this This instance.
     */
    public function setMenu(MenuInterface $menu)
    {
        $this->menu = $menu;

        return $this;
    }
}
