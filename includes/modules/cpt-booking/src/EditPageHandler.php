<?php

namespace RebelCode\EddBookings\CustomPostType\Booking;

use \Dhii\App\AppInterface;
use \Dhii\Di\FactoryInterface;
use \Psr\EventManager\EventManagerInterface;
use \RebelCode\EddBookings\CustomPostType;
use \RebelCode\EddBookings\Model\Booking;
use \RebelCode\EddBookings\System\Component\AbstractBaseComponent;

/**
 * Description of EditPageSaveHandler
 *
 * @since [*next-version*]
 */
class EditPageHandler extends AbstractBaseComponent
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
     * The custom post type.
     *
     * @since [*next-version*]
     *
     * @var CustomPostType
     */
    protected $cpt;

    /**
     * The factory - used to create booking instances.
     *
     * @since [*next-version*]
     *
     * @var FactoryInterface
     */
    protected $factory;

    /**
     * Constructor.
     *
     * @since [*next-version*]
     *
     * @param AppInterface $app The parent app instance.
     * @param EventManagerInterface $eventManager The event manager.
     * @param CustomPostType $cpt The custom post type.
     */
    public function __construct(
        AppInterface $app,
        EventManagerInterface $eventManager,
        CustomPostType $cpt,
        FactoryInterface $factory
    ) {
        parent::__construct($app);

        $this->setEventManager($eventManager)
            ->setCpt($cpt)
            ->setFactory($factory);
    }

    public function onAppReady()
    {
        $this->getEventManager()->attach(
            'add_meta_boxes',
            $this->_callback('removeWpMetaboxes')
        );

        $this->getEventManager()->attach(
            'save_post',
            $this->_callback('saveBooking')
        );
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
     * Gets the custom post type.
     *
     * @since [*next-version*]
     *
     * @return CustomPostType The custom post type.
     */
    public function getCpt()
    {
        return $this->cpt;
    }

    /**
     * Sets the custom post type
     *
     * @since [*next-version*]
     *
     * @param CustomPostType $cpt The new custom post type instance.
     *
     * @return $this This instance.
     */
    public function setCpt(CustomPostType $cpt)
    {
        $this->cpt = $cpt;

        return $this;
    }

    /**
     * Gets the factory instance.
     *
     * @since [*next-version*]
     *
     * @return FactoryInterface The factory instance.
     */
    public function getFactory()
    {
        return $this->factory;
    }

    /**
     * Sets the factory instance.
     *
     * @since [*next-version*]
     *
     * @param FactoryInterface $factory The new factory instance.
     *
     * @return $this This instance.
     */
    public function setFactory(FactoryInterface $factory)
    {
        $this->factory = $factory;

        return $this;
    }

    public function removeWpMetaboxes()
    {
        \remove_meta_box('submitdiv', $this->getCpt()->getSlug(), 'side');
    }

    protected function _canSave(\WP_Post $post)
    {
        if (\wp_is_post_revision($post->ID)) {
            return false;
        }
        if ($post->post_type !== $this->getCpt()->getSlug()) {
            return false;
        }
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return false;
        }
        if (filter_has_var(INPUT_POST, 'bulk_edit')) {
            return false;
        }
        if (!current_user_can('edit_post', $post->ID)) {
            return false;
        }

        return true;
    }

    public function saveBooking($postId, \WP_Post $post, $update)
    {
        if (!$this->_canSave($post)) {
            return $postId;
        }
        
        $bookingData = filter_input(INPUT_POST, 'booking', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);

        if (!$bookingData) {
            return;
        }

        $bookingData['id']    = $postId;
        $bookingData['status'] = filter_input(INPUT_POST, 'post_status');

        /* @var $booking Booking */
        $booking = $this->getFactory()->make('booking');
        $booking->setData($bookingData)
            ->getResourceModel()
            ->save($booking);

        return $postId;
    }
}
