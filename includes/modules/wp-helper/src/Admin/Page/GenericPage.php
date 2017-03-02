<?php

namespace RebelCode\Wp\Admin\Page;

/**
 * A generic implementation of a page that exposes its full set of setter methods for generic usage.
 *
 * @since [*next-version*]
 */
class GenericPage extends AbstractBasePage
{
    /**
     * The default required user capability.
     *
     * @since [*next-version*]
     */
    const DEFAULT_REQUIRED_CAPABILITY = 'read';

    /**
     * The page content.
     *
     * @since [*next-version*]
     *
     * @var type
     */
    protected $content;

    /**
     * Constructor.
     *
     * @since [*next-version*]
     *
     * @param string $id The page ID.
     * @param string $title The page title.
     * @param string|BlockInterface $content The page content or block instance to render.
     * @param string $requiredCapability The required user capability to display the page.
     */
    public function __construct(
        $id,
        $title,
        $content,
        $requiredCapability = self::DEFAULT_REQUIRED_CAPABILITY
    ) {
        $this->setId($id)
            ->setTitle($title)
            ->setContent($content)
            ->setRequiredCapability($requiredCapability);
    }

    /**
     * {@inheritdoc}
     *
     * @since [*next-version*]
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Sets the content for this page.
     *
     * @since [*next-version*]
     *
     * @param string|BlockInterface The string content or a block instance.
     *
     * @return $this
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Sets the page ID.
     *
     * @since [*next-version*]
     *
     * @param string $id
     *
     * @return $this
     */
    public function setId($id)
    {
        $this->_setId($id);

        return $this;
    }

    /**
     * Sets the page title.
     *
     * @param string $title
     *
     * @return $this
     */
    public function setTitle($title)
    {
        $this->_setTitle($title);

        return $this;
    }

    /**
     * Sets the required user capability to display this page.
     *
     * @param string $requiredCapability
     *
     * @return $this
     */
    public function setRequiredCapability($requiredCapability)
    {
        $this->_setRequiredCapability($requiredCapability);

        return $this;
    }
}
