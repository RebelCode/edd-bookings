<?php

namespace RebelCode\Wp\Admin\Menu;

/**
 * Description of SubMenu
 *
 * @since [*next-version*]
 */
class SubMenu extends AbstractSubMenu implements SubMenuInterface
{
    /**
     * The default required user capability.
     *
     * @since [*next-version*]
     */
    const DEFAULT_CAPABILITY = 'read';

    /**
     * Constructor.
     *
     * @since [*next-version*]
     *
     * @param TopLevelMenuInterface|string $parentMenu The parent menu instance or ID.
     * @param string $id The menu ID.
     * @param string $label The menu label.
     * @param callable|PageInterface|BlockInterface|string|null $content The menu content.
     * @param string $icon The menu icon.
     * @param string $capability The required user capability to display the menu.
     * @param int $position The menu position.
     */
    public function __construct(
        $parentMenu,
        $id,
        $label,
        $content = '',
        $capability = self::DEFAULT_CAPABILITY
    ) {
        $this->_setParentMenu($parentMenu)
            ->_setId($id)
            ->_setLabel($label)
            ->_setContent($content)
            ->_setCapability($capability);
    }

    /**
     * {@inheritdoc}
     *
     * @since [*next-version*]
     */
    public function getId()
    {
        return $this->_getId();
    }

    /**
     * {@inheritdoc}
     *
     * @since [*next-version*]
     */
    public function getLabel()
    {
        return $this->_getLabel();
    }

    /**
     * {@inheritdoc}
     *
     * @since [*next-version*]
     */
    public function getContent()
    {
        return $this->_getContent();
    }

    /**
     * {@inheritdoc}
     *
     * @since [*next-version*]
     */
    public function getCapability()
    {
        return $this->_getCapability();
    }

    /**
     * {@inheritdoc}
     *
     * @since [*next-version*]
     */
    public function getParentMenu()
    {
        return $this->_getParentMenu();
    }

    /**
     * Sets the menu ID.
     *
     * @since [*next-version*]
     *
     * @param string $id
     *
     * @return $this
     */
    public function setId($id)
    {
        return $this->_setId($id);
    }

    /**
     * Sets the menu label.
     *
     * @since [*next-version*]
     *
     * @param string $label
     *
     * @return $this
     */
    public function setLabel($label)
    {
        return $this->_setLabel($label);
    }

    /**
     * Sets the content to be displayed when this menu is selected.
     *
     * @since [*next-version*]
     *
     * @param callable|PageInterface|BlockInterface|string|null $content A callback function, page, block, URL or null.
     *
     * @return $this
     */
    public function setContent($content)
    {
        return $this->_setContent($content);
    }

    /**
     * Sets the required user capability for this menu to be displayed.
     *
     * @since [*next-version*]
     *
     * @param string $capability The required user capability.
     *
     * @return $this
     */
    public function setCapability($capability)
    {
        return $this->_setCapability($capability);
    }

    /**
     * Sets the parent top-level menu.
     *
     * @since [*next-version*]
     *
     * @param TopLevelMenuInterface|string $parentMenu The parent menu instance or ID.
     *
     * @return $this
     */
    public function setParentMenu($parentMenu)
    {
        return $this->_setParentMenu($parentMenu);
    }

    /**
     * {@inheritdoc}
     *
     * @since [*next-version*]
     */
    public function register()
    {
        return $this->_register();
    }
}
