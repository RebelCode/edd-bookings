<?php

namespace RebelCode\EddBookings\CustomPostType\Booking;

use \Dhii\App\AppInterface;
use \Psr\EventManager\EventManagerInterface;
use \RebelCode\WordPress\Admin\Metabox\MetaBox;
use \RebelCode\WordPress\Admin\Metabox\MetaBoxInterface;

/**
 * Component for the Booking Edit meta box.
 *
 * @since [*next-version*]
 */
class EditMetaBox extends MetaBox
{
    /**
     * The meta box.
     *
     * @since [*next-version*]
     *
     * @var MetaBoxInterface
     */
    protected $metaBox;

    /**
     * The event manager.
     *
     * @since [*next-version*]
     *
     * @var EventManagerInterface
     */
    protected $eventManager;

    /**
     * Constructor.
     *
     * @since [*next-version*]
     *
     * @param AppInterface $app The parent app.
     * @param MetaBoxInterface $metaBox The meta box.
     * @param EventManagerInterface $eventManager The event manager.
     */
    public function __construct(AppInterface $app, MetaBoxInterface $metaBox, EventManagerInterface $eventManager)
    {
        parent::__construct($app);

        $this->setMetaBox($metaBox)
            ->setEventManager($eventManager);
    }

    /**
     * {@inheritdoc}
     *
     * @since [*next-version*]
     */
    public function onAppReady()
    {
        parent::onAppReady();

        $this->getEventManager()
            ->attach('admin_enqueue_scripts', $this->_callback('disableAutoSave'));
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
     * Gets the meta box.
     *
     * @since [*next-version*]
     *
     * @return MetaBoxInterface The meta box instance.
     */
    public function getMetaBox()
    {
        return $this->metaBox;
    }

    /**
     * Sets the meta box.
     *
     * @since [*next-version*]
     *
     * @param MetaBoxInterface $metaBox The meta box instance.
     *
     * @return $this This instance.
     */
    public function setMetaBox(MetaBoxInterface $metaBox)
    {
        $this->metaBox = $metaBox;

        return $this;
    }

    /**
     * Registers the metabox.
     *
     * @since [*next-version*]
     *
     * @return $this This instance.
     */
    public function registerMetaBox()
    {
        $this->getMetaBox()->register();

        return $this;
    }

    /**
     * Disables autosave for this CPT.
     *
     * WordPress autosave is a JS script file that uses AJAX to submit autosaves.
     *
     * @since [*next-version*]
     */
    public function disableAutoSave()
    {
        if (\get_post_type() === self::SLUG) {
            \wp_dequeue_script('autosave');
        }
    }
}
