<?php

namespace RebelCode\Wp\Admin\Menu;

/**
 * A WordPress top-level admin menu.
 *
 * @since [*next-version*]
 */
class TopLevelMenu extends AbstractTopLevelMenu implements TopLevelMenuInterface
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
     * @param string $id The menu ID.
     * @param string $label The menu label.
     * @param callable|PageInterface|BlockInterface|string|null $content The menu content.
     * @param string $icon The menu icon.
     * @param string $requiredCapability The required user capability to display the menu.
     * @param int $position The menu position.
     */
    public function __construct(
        $id,
        $label,
        $content = '',
        $icon = '',
        $requiredCapability = self::DEFAULT_CAPABILITY,
        $position = null
    ) {
        $this->_setId($id)
            ->_setLabel($label)
            ->_setContent($content)
            ->_setIcon($icon)
            ->_setRequiredCapability($requiredCapability)
            ->_setPosition($position);
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
    public function getIcon()
    {
        return $this->_getIcon();
    }

    /**
     * {@inheritdoc}
     *
     * @since [*next-version*]
     */
    public function getRequiredCapability()
    {
        return $this->_getRequiredCapability();
    }

    /**
     * {@inheritdoc}
     *
     * @since [*next-version*]
     */
    public function getPosition()
    {
        return $this->_getPosition();
    }

    public function setContent($content)
    {
        return $this->_setContent($content);
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

    /**
     * Sets the icon to show for this menu.
     *
     * @since [*next-version*]
     *
     * @param string $icon A URL or dashicons icon name.
     */
    public function setIcon($icon)
    {
        return $this->_setIcon($icon);
    }

    /**
     * Sets the menu position.
     *
     * @since [*next-version*]
     *
     * @param int $position
     *
     * @return $this
     */
    public function setPosition($position)
    {
        return $this->_setPosition($position);
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
