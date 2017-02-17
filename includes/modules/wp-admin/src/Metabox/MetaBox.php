<?php

namespace RebelCode\WordPress\Admin\Metabox;

use \Dhii\App\AppInterface;
use \Dhii\WpEvents\EventManager;
use \Psr\EventManager\EventManagerInterface;
use \RebelCode\EddBookings\System\Component\AbstractBaseComponent;

/**
 * Basic component implementation for a metabox.
 *
 * @since [*next-version*]
 */
class MetaBox extends AbstractBaseComponent implements MetaBoxInterface
{
    /**
     * The event manager.
     *
     * @since [*next-version*]
     *
     * @var EventManagerInterface
     */
    protected $eventManager;

    /**
     * The metabox ID.
     *
     * @since [*next-version*]
     *
     * @var string
     */
    protected $id;

    /**
     * The metabox title.
     *
     * @since [*next-version*]
     *
     * @var string
     */
    protected $title;

    /**
     * The metabox content callback.
     *
     * @var callable
     */
    protected $callback;

    /**
     * The metabox context.
     *
     * @since [*next-version*]
     *
     * @var string
     */
    protected $ctx;

    /**
     * The metabox priority.
     *
     * @var string
     */
    protected $priority;

    /**
     * The metabox screen.
     *
     * @since [*next-version*]
     *
     * @var string|array|WP_Screen
     */
    protected $screen;

    /**
     * Additional args to pass to the metabox callback.
     *
     * @since [*next-version*]
     *
     * @var array
     */
    protected $args;

    /**
     * Constructor.
     *
     * @since [*next-version*]
     *
     * @param string $id The ID.
     * @param string $title The title.
     * @param callable $callback The content callback.
     * @param string $ctx The context. Default: static::CTX_ADVANCED
     * @param string $priority The priority. Default: static::PRIORITY_DEFAULT
     * @param string|array|WP_Screen $screen The screen. Default: null
     */
    public function __construct(
        AppInterface $app,
        EventManager $eventManager,
        $id,
        $title,
        $callback,
        $ctx = self::CTX_ADVANCED,
        $priority = self::PRIORITY_DEFAULT,
        $screen = null,
        $args = array()
    ) {
        parent::__construct($app);

        $this->setEventManager($eventManager)
            ->setId($id)
            ->setTitle($title)
            ->setCallback($callback)
            ->setContext($ctx)
            ->setPriority($priority)
            ->setScreen($screen)
            ->setArgs($args);
    }

    /**
     * {@inheritdoc}
     *
     * @since [*next-version*]
     */
    public function onAppReady()
    {
        $this->getEventManager()->attach('add_meta_boxes', $this->_callback('register'));
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

    /**
     * {@inheritdoc}
     *
     * @since [*next-version*]
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     *
     * @since [*next-version*]
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * {@inheritdoc}
     *
     * @since [*next-version*]
     */
    public function getCallback()
    {
        return $this->callback;
    }

    /**
     * {@inheritdoc}
     *
     * @since [*next-version*]
     */
    public function getContext()
    {
        return $this->ctx;
    }

    /**
     * {@inheritdoc}
     *
     * @since [*next-version*]
     */
    public function getPriority()
    {
        return $this->priority;
    }

    /**
     * {@inheritdoc}
     *
     * @since [*next-version*]
     */
    public function getScreen()
    {
        return $this->screen;
    }

    /**
     * Gets the arguments to pass to the content callback.
     *
     * @since [*next-version*]
     *
     * @return array An array of arguments.
     */
    public function getArgs()
    {
        return $this->args;
    }

    /**
     * Sets the ID.
     *
     * @since [*next-version*]
     *
     * @param string $id The new ID.
     *
     * @return $this This instance.
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Sets the title.
     *
     * @since [*next-version*]
     *
     * @param string $title The new title.
     *
     * @return $this This instance.
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Sets the content callback.
     *
     * @since [*next-version*]
     *
     * @param string $callback The new content callback.
     *
     * @return $this This instance
     */
    public function setCallback($callback)
    {
        $this->callback = $callback;

        return $this;
    }

    /**
     * Sets the context.
     *
     * @since [*next-version*]
     *
     * @param string $ctx The new context.
     *
     * @return $this This instance
     */
    public function setContext($ctx)
    {
        $this->ctx = $ctx;

        return $this;
    }

    /**
     * Sets the priority.
     *
     * @since [*next-version*]
     *
     * @param string $priority The new priority.
     *
     * @return $this This instance
     */
    public function setPriority($priority)
    {
        $this->priority = $priority;

        return $this;
    }

    /**
     * Sets the screen.
     *
     * @since [*next-version*]
     *
     * @param string|array|WP_Screen $screen The screen.
     *
     * @return $this This instance
     */
    public function setScreen($screen)
    {
        $this->screen = $screen;

        return $this;
    }

    /**
     * Sets the arguments to pass to the content callback.
     *
     * @since [*next-version*]
     *
     * @param array $args An array of arguments.
     *
     * @return $this This instance.
     */
    public function setArgs(array $args)
    {
        $this->args = $args;

        return $this;
    }

    /**
     * Renders the metabox.
     *
     * @since [*next-version*]
     *
     * @return $this This instance.
     */
    public function render()
    {
        $callback = $this->getCallback();
        $content  = is_callable($callback)
            ? $callback()
            : (string) $callback;

        echo $content;

        return $this;
    }

    /**
     * {@inheritdoc}
     *
     * @since [*next-version*]
     */
    public function register()
    {
        \add_meta_box(
            $this->getId(),
            $this->getTitle(),
            array($this, 'render'),
            $this->getScreen(),
            $this->getContext(),
            $this->getPriority(),
            $this->getArgs()
        );
    }
}
