<?php

namespace RebelCode\WordPress\Admin\Menu;

use \RebelCode\WordPress\Admin\Page;

/**
 * Represents a WordPress sub-menu.
 *
 * @since [*next-version*]
 */
class SubMenu extends AbstractMenu
{
    /**
     * The parent menu ID.
     *
     * @since [*next-version*]
     *
     * @var int
     */
    protected $parentId;

    /**
     * Constructor.
     *
     * @since [*next-version*]
     *
     * @param int       $parentId The parent menu ID.
     * @param string    $id       The menu ID.
     * @param string    $label    The menu label.
     * @param Page $page     The admin page instance.
     */
    public function __construct($parentId, $id, $label, Page $page)
    {
        $this->setParentId($parentId)
            ->setId($id)
            ->setLabel($label)
            ->setPage($page);
    }

    /**
     * Gets the parent menu's ID.
     *
     * @since [*next-version*]
     *
     * @return int The parent menu's ID.
     */
    public function getParentId()
    {
        return $this->parentId;
    }

    /**
     * Sets the parent menu's ID.
     *
     * @since [*next-version*]
     *
     * @param int $parentId The parent menu's ID.
     *
     * @return $this This instance.
     */
    public function setParentId($parentId)
    {
        $this->parentId = $parentId;

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

        \add_submenu_page(
            $this->getParentId(),
            $page->getTitle(),
            $this->getLabel(),
            $page->getCapability(),
            $this->id,
            $page
        );
    }
}
