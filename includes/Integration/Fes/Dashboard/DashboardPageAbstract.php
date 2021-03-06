<?php

namespace Aventura\Edd\Bookings\Integration\Fes\Dashboard;

use \Aventura\Edd\Bookings\Controller\ControllerAbstract;
use \Aventura\Edd\Bookings\Plugin;

/**
 * Abstract implementation of an FES dashboard page.
 *
 * @author Miguel Muscat <miguelmuscat93@gmail.com>
 */
abstract class DashboardPageAbstract extends ControllerAbstract implements DashboardPageInterface
{

    /**
     * The page ID.
     * 
     * @var string
     */
    protected $id;

    /**
     * The page title.
     * 
     * @var string
     */
    protected $title;

    /**
     * The page icon.
     * 
     * @var string
     */
    protected $icon;

    /**
     * Constructs a new instance.
     * 
     * @param Plugin $plugin The parent plugin instance.
     * @param string $id The page ID.
     * @param string $title The page title.
     * @param string $icon The page icon.
     */
    public function __construct(Plugin $plugin, $id, $title = null, $icon = '')
    {
        parent::__construct($plugin);
        $this->id = $id;
        $this->title = $title;
        $this->icon = $icon;
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * {@inheritdoc}
     */
    public function getIcon()
    {
        return $this->icon;
    }

    /**
     * Gets the menu item.
     * 
     * @return array
     */
    public function getMenuItem()
    {
        return array(
            'task' => $this->getId(),
            'name' => $this->getTitle(),
            'icon' => $this->getIcon()
        );
    }

    /**
     * Registers the menu item.
     * 
     * @param array $menu Input filtered menu items.
     * @return array Output menu items.
     */
    public function registerMenuItem($menu)
    {
        if (is_null($this->getTitle())) {
            return $menu;
        }
        $index = array_search('profile', array_keys($menu));
        if ($index === false) {
            return $menu;
        }
        $menuItem = array($this->getId() => $this->getMenuItem());
        array_splice($menu, $index, 0, $menuItem);
        return $menu;
    }

    /**
     * Signals FES that this page has a custom task.
     * 
     * @param bool $isCustom True/false filter value that determins if the task is custom.
     * @param string $task The task id string.
     * @return bool The filtered boolean. True for custom, false for not.
     */
    public function signalCustomTask($isCustom, $task)
    {
        return (bool) $isCustom || $task === $this->getId();
    }

    /**
     * {@inheritdoc}
     */
    abstract public function render();

    /**
     * {@inheritdoc}
     */
    public function hook()
    {
        add_filter('fes_vendor_dashboard_menu', array($this, 'registerMenuItem'));
        add_action(sprintf('fes_custom_task_%s', $this->getId()), array($this, 'render'));
        add_filter('fes_signal_custom_task', array($this, 'signalCustomTask'), 10, 2);
    }

}
