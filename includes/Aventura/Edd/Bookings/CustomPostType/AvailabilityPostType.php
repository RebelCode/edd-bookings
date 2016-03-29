<?php

namespace Aventura\Edd\Bookings\CustomPostType;

use \Aventura\Edd\Bookings\CustomPostType;
use \Aventura\Edd\Bookings\Plugin;
use \Aventura\Edd\Bookings\Renderer\AvailabilityRenderer;

/**
 * The Availability custom post type.
 *
 * @author Miguel Muscat <miguelmuscat93@gmail.com>
 */
class AvailabilityPostType extends CustomPostType
{

    /**
     * The CPT slug name.
     */
    const SLUG = 'edd_bk_availability';

    /**
     * Constructs a new instance.
     * 
     * @param Plugin $plugin The parent plugin instance.
     */
    public function __construct(Plugin $plugin)
    {
        parent::__construct($plugin, self::SLUG);
        $this->generateLabels('Schedule', 'Schedules');
        $this->setDefaultProperties();
    }

    /**
     * Registers the metaboxes.
     */
    public function addMetaboxes()
    {
        global $post;
        $metaboxArgs = compact('post');
        $textDomain = $this->getPlugin()->getI18n()->getDomain();
        \add_meta_box('edd-bk-availability-options', __('Options', $textDomain), array($this, 'renderOptionsMetabox'),
                static::SLUG, 'normal', 'core', $metaboxArgs);
        $screen = \get_current_screen();
        if ($screen->action !== 'add') {
            \add_meta_box('edd-bk-availability-services', __('Downloads using this schedule', $textDomain),
                    array($this, 'renderServicesMetabox'), static::SLUG, 'side', 'core', $metaboxArgs);
        }
        if ($screen->action !== 'add') {
            \add_meta_box('edd-bk-availability-calendar', __('Schedule Calendar', $textDomain),
                    array($this, 'renderCalendarMetabox'), static::SLUG, 'normal', 'core', $metaboxArgs);
        }
    }

    /**
     * Renders the options metabox.
     */
    public function renderOptionsMetabox($currentPost, $metabox)
    {
        $post = $metabox['args']['post'];
        $availability = (empty($post->ID))
                ? $this->getPlugin()->getAvailabilityController()->getFactory()->create(array('id' => 0))
                : $this->getPlugin()->getAvailabilityController()->get($post->ID);
        $renderer = new AvailabilityRenderer($availability);
        echo $renderer->render();
    }
    
    /**
     * Renders the services metabox.
     */
    public function renderServicesMetabox($currentPost, $metabox)
    {
        $post = $metabox['args']['post'];
        $textDomain = $this->getPlugin()->getI18n()->getDomain();
        $services = $this->getPlugin()->getServiceController()->getServicesForAvailability($post->ID);
        $bookings = array();
        $total = 0;
        foreach ($services as $service) {
            $serviceId = $service->getId();
            $link = sprintf(\admin_url('post.php?post=%s&action=edit'), $serviceId);
            $name = \get_the_title($serviceId);
            $bookings[$serviceId] = $this->getPlugin()->getBookingController()->getBookingsForService($serviceId);
            $numBookings = count($bookings[$serviceId]);
            $total += $numBookings;
            printf('<p><strong><a href="%s">%s</a>:</strong> %d bookings</p>', $link, $name, $numBookings);
        }
        printf('<hr/><p><strong>%s</strong> %d</p>', __('Total bookings:', $textDomain), $total);
    }
    
    /**
     * Renders the schedule calendar metabox.
     */
    public function renderCalendarMetabox($currentPost, $metabox)
    {
        $post = $metabox['args']['post'];
        $renderer = new \Aventura\Edd\Bookings\Renderer\BookingsCalendarRenderer($this->getPlugin());
        echo $renderer->render(array(
                'wrap'   => false,
                'header' => false,
                'data'   => array(
                        'schedules' => $post->ID
                )
        ));
    }

    /**
     * Callback triggered when an availability is saved or updated.
     * 
     * @param integer $postId The availability post ID.
     * @param WP_Post $post The availability post object.
     */
    public function onSave($postId, $post) {
        if ($this->_guardOnSave($postId, $post)) {
            check_admin_referer('edd_bk_save_meta', 'edd_bk_availability');
            // Save the download meta
            $meta = $this->extractMeta();
            $this->getPlugin()->getAvailabilityController()->saveMeta($postId, $meta);
        }
    }
    
    /**
     * Extracts the meta data from submitted POST.
     * 
     * @return array The extracted meta data as an associative array of key => value pairs.
     */
    public function extractMeta() {
        // Filter input post data
        $timetableId = filter_input(INPUT_POST, 'edd-bk-availability-timetable-id', FILTER_VALIDATE_INT);
        // Generate meta
        $meta = array(
                'timetable_id'  =>  $timetableId
        );
        // Filter and return
        $filtered = \apply_filters('edd_bk_availability_saved_meta', $meta);
        return $filtered;
    }
    
    /**
     * Sets the properties to their default.
     * 
     * @return AvailabilityPostType This instance.
     */
    public function setDefaultProperties()
    {
        $properties = array(
                'public'       => false,
                'show_ui'      => true,
                'has_archive'  => false,
                'show_in_menu' => 'edd-bookings',
                'supports'     => array('title')
        );
        $filtered = \apply_filters('edd_bk_availability_cpt_properties', $properties);
        $this->setProperties($filtered);
        return $this;
    }

    /**
     * Registers the WordPress hooks.
     */
    public function hook()
    {
        $this->getPlugin()->getHookManager()
                ->addAction('init', $this, 'register', 11)
                ->addAction('add_meta_boxes', $this, 'addMetaboxes')
                ->addAction('save_post', $this, 'onSave', 10, 2);
    }

}
