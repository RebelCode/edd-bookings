<?php

namespace RebelCode\WordPress\Admin\Menu;

/**
 * Represents a single menu on the WordPress admin menu bar.
 *
 * @since [*next-version*]
 */
class Menu extends AbstractMenu
{
    /**
     * The menu icon URL or dashicon name.
     *
     * @since [*next-version*]
     *
     * @var string
     */
    protected $icon;

    /**
     * The menu sub-menus.
     *
     * @since [*next-version*]
     *
     * @var SubMenu[]
     */
    protected $subMenus;

    /**
     * Constructor.
     *
     * @since [*next-version*]
     *
     * @param string $id The menu ID.
     * @param string $label The menu label.
     * @param Page $page The admin page instance.
     * @param string $icon The menu icon URL or dashicon name. Default: ''
     * @param int $position The menu position. Default: 100
     */
    public function __construct($id, $label, callable $page, $icon = '', $position = 100)
    {
        $this->setId($id)
            ->setLabel($label)
            ->setIcon($icon)
            ->setPage($page)
            ->setPosition($position);

        $this->subMenus = array();
    }

    /**
     * Gets the menu icon.
     *
     * @since [*next-version*]
     *
     * @return string The menu icon URL or dashicon name.
     */
    public function getIcon()
    {
        return $this->icon;
    }

    /**
     * Gets the menu position.
     *
     * @since [*next-version*]
     *
     * @return int The menu position.
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * Sets the menu icon.
     *
     * @since [*next-version*]
     *
     * @param string $icon The menu icon URL or dashicon name.
     *
     * @return $this This instance.
     */
    public function setIcon($icon)
    {
        $this->icon = $icon;

        return $this;
    }

    /**
     * Sets the menu position.
     *
     * @since [*next-version*]
     *
     * @param int $position The menu position.
     *
     * @return $this This instance.
     */
    public function setPosition($position)
    {
        $this->position = $position;

        return $this;
    }

    /**
     * Gets the submenus for this menu.
     *
     * @since [*next-version*]
     *
     * @return SubMenu[] An array of submenu instances.
     */
    public function getSubMenus()
    {
        return $this->subMenus;
    }

    /**
     * Adds a submenu to this menu.
     *
     * @since [*next-version*]
     *
     * @param SubMenu $subMenu The submenu instance to add.
     *
     * @return $this This instance.
     */
    public function addSubMenu(SubMenu $subMenu)
    {
        $this->subMenus[$subMenu->getId()] = $subMenu;

        return $this;
    }

    /**
     * {@inheritdoc}
     *
     * @since [*next-version*]
     */
    public function register()
    {
        $page = $this->getPage();

        \add_menu_page(
            $page->getTitle(),
            $this->getLabel(),
            $page->getCapability(),
            $this->id,
            $page,
            $this->getIcon(),
            $this->getPosition()
        );

        $this->_registerSubMenus();

        return $this;
    }

    /**
     * Registers the submenus for this menu.
     *
     * @since [*next-version*]
     *
     * @return $this This instance.
     */
    protected function registerSubMenus()
    {
        /* @var $sorted SubMenu[] */
        $sorted = usort($this->getSubMenus(), function(SubMenu $sm1, SubMenu $sm2) {
            return ($sm1->getPosition() < $sm2->getPosition())
                ? -1
                : 1;
        });

        foreach ($sorted as $submenu) {
            $submenu->register();
        }

        return $this;
    }
}
