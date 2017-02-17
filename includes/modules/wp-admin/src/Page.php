<?php

namespace RebelCode\WordPress\Admin;

/**
 * Represents a WP Admin page.
 *
 * @since [*next-version*]
 */
class Page
{
    /**
     * The page title.
     *
     * @since [*next-version*]
     *
     * @var string
     */
    protected $title;

    /**
     * The required user capability to view this page.
     *
     * @since [*next-version*]
     *
     * @var string
     */
    protected $capability;

    /**
     * The page content.
     *
     * @since [*next-version*]
     *
     * @var string
     */
    protected $content;

    /**
     * Constructor.
     *
     * @since [*next-version*]
     *
     * @param string $title The page title.
     * @param mixed $content The page content: anything that can be casted into a string.
     * @param string $capability The user capability requirement. Default: 'manage_options'
     */
    public function __construct($title, $content, $capability = 'manage_options')
    {
        $this->setTitle($title)
            ->setContent($content)
            ->setCapability($capability);
    }

    /**
     * Gets the title.
     *
     * @since [*next-version*]
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Gets the user capability requirement.
     *
     * @since [*next-version*]
     *
     * @return string
     */
    public function getCapability()
    {
        return $this->capability;
    }

    /**
     * Gets the page content.
     *
     * @since [*next-version*]
     *
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Sets the page title.
     *
     * @since [*next-version*]
     *
     * @param string $title The new page title.
     *
     * @return $this This instance.
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Sets the user capability requirement.
     *
     * @since [*next-version*]
     *
     * @param string $capability The new user capability requirement.
     *
     * @return $this This instance.
     */
    public function setCapability($capability)
    {
        $this->capability = $capability;

        return $this;
    }

    /**
     * Sets the page content.
     *
     * @since [*next-version*]
     *
     * @param mixed $content The new page content: anything that can be casted into a string.
     *
     * @return $this This instance.
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Renders the page.
     *
     * @since [*next-version*]
     *
     * @return string The rendered output.
     */
    public function render()
    {
        return strval($this->getContent());
    }

    /**
     * Calls {@see AdminPage::render()} and prints the output.
     *
     * @since [*next-version*]
     */
    public function __invoke()
    {
        echo $this->render();
    }
}
