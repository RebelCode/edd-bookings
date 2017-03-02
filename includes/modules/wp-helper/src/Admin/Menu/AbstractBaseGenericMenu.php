<?php

namespace RebelCode\Wp\Admin\Menu;

/**
 * Base implementation of a generic and modifiable WordPress menu.
 *
 * @since [*next-version*]
 */
abstract class AbstractBaseGenericMenu extends AbstractBaseMenu
{
    /**
     * The default required user capability.
     *
     * @since [*next-version*]
     */
    const DEFAULT_CAPABILITY = 'read';

    /**
     * Internal constructor.
     *
     * @since [*next-version*]
     *
     * @param string $id The menu ID.
     * @param string $label The menu label.
     * @param callable|PageInterface|BlockInterface|string|null $content The menu content.
     * @param string $requiredCapability The required user capability to display the menu.
     */
    protected function _construct(
        $id,
        $label,
        $content = '',
        $requiredCapability = self::DEFAULT_CAPABILITY
    ) {
        $this->setId($id)
            ->setLabel($label)
            ->setContent($content)
            ->setRequiredCapability($requiredCapability);
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
     * @param string $requiredCapability The required user capability.
     *
     * @return $this
     */
    public function setRequiredCapability($requiredCapability)
    {
        return $this->_setRequiredCapability($requiredCapability);
    }
}
