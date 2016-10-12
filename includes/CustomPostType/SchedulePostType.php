<?php

namespace Aventura\Edd\Bookings\CustomPostType;

use \Aventura\Edd\Bookings\CustomPostType;
use \Aventura\Edd\Bookings\Plugin;
use \Aventura\Edd\Bookings\Renderer\ScheduleRenderer;
use \Aventura\Edd\Bookings\Renderer\BookingsCalendarRenderer;

/**
 * The Schedule custom post type.
 *
 * @author Miguel Muscat <miguelmuscat93@gmail.com>
 */
class SchedulePostType extends CustomPostType
{

    /**
     * The CPT slug name.
     */
    const SLUG = 'edd_bk_schedule';

    /**
     * Constructs a new instance.
     * 
     * @param Plugin $plugin The parent plugin instance.
     */
    public function __construct(Plugin $plugin)
    {
        parent::__construct($plugin, self::SLUG);
        $this->generateLabels(__('Schedule', 'eddbk'), __('Schedules', 'eddbk'))
               ->setLabel('all_items', __('Schedules', 'eddbk'))
                ->setDefaultProperties();
    }

    /**
     * Registers the metaboxes.
     */
    public function addMetaboxes()
    {
        // Query fix
        global $post, $wp_query;
        $wp_query->post = $post;

        \add_meta_box('edd-bk-schedule-options', __('Options', 'eddbk'), array($this, 'renderOptionsMetabox'),
                static::SLUG, 'normal', 'core');
        $screen = \get_current_screen();
        if ($screen->action !== 'add') {
            \add_meta_box('edd-bk-schedule-calendar', __('Schedule Calendar', 'eddbk'),
                    array($this, 'renderCalendarMetabox'), static::SLUG, 'normal', 'core');
            \add_meta_box('edd-bk-calendar-booking-info', __('Booking Info', 'eddbk'),
                    array($this, 'renderBookingInfoMetabox'), static::SLUG, 'side', 'core');
            \add_meta_box('edd-bk-schedule-services', __('Downloads using this schedule', 'eddbk'),
                    array($this, 'renderServicesMetabox'), static::SLUG, 'side', 'core');
        }
    }

    /**
     * Renders the options metabox.
     */
    public function renderOptionsMetabox($post)
    {
        $schedule = (empty($post->ID))
                ? $this->getPlugin()->getScheduleController()->getFactory()->create(array('id' => 0))
                : $this->getPlugin()->getScheduleController()->get($post->ID);
        $renderer = new ScheduleRenderer($schedule);
        echo $renderer->render();
    }
    
    /**
     * Renders the services metabox.
     */
    public function renderServicesMetabox($post)
    {
        $services = $this->getPlugin()->getServiceController()->getServicesForSchedule($post->ID);
        $bookings = array();
        $total = 0;
        if (!empty($services)) {
            foreach ($services as $service) {
                $serviceId = $service->getId();
                $link = sprintf(\admin_url('post.php?post=%s&action=edit'), $serviceId);
                $name = \get_the_title($serviceId);
                $bookings[$serviceId] = $this->getPlugin()->getBookingController()->getBookingsForService($serviceId);
                $numBookings = count($bookings[$serviceId]);
                $total += $numBookings;
                $numBookingsStr = sprintf(_n('%d booking', '%d bookings', $numBookings, 'eddbk'), $numBookings);
                printf('<p><strong><a href="%s">%s</a>:</strong> %s</p>', $link, $name, $numBookingsStr);
            }
            printf('<hr/><p><strong>%s</strong> %d</p>', __('Total bookings:', 'eddbk'), $total);
        } else {
            printf('<p>%s</p>', __('There are no Downloads using this Schedule.', 'eddbk'));
        }
    }
    
    /**
     * Renders the schedule calendar metabox.
     */
    public function renderCalendarMetabox($post)
    {
        $renderer = new BookingsCalendarRenderer($this->getPlugin());
        echo $renderer->render(array(
                'wrap'     => false,
                'header'   => false,
                'infopane' => false,
                'data'     => array(
                        'schedules' => $post->ID
                )
        ));
    }
    
    /**
     * Renders the booking info metabox.
     * 
     * This metabox will be populated with booking info when a booking is cliked on the calendar.
     */
    public function renderBookingInfoMetabox($post)
    {
        echo BookingsCalendarRenderer::renderInfoPane(array(
                'header'    =>  false
        ));
    }

    /**
     * Callback triggered when a schedule is saved or updated.
     * 
     * @param integer $postId The schedule post ID.
     * @param WP_Post $post The schedule post object.
     */
    public function onSave($postId, $post) {
        if ($this->_guardOnSave($postId, $post)) {
            check_admin_referer('edd_bk_save_meta', static::SLUG);
            // Save the download meta
            $meta = $this->extractMeta($postId);
            $this->getPlugin()->getScheduleController()->saveMeta($postId, $meta);
        }
    }
    
    /**
     * Extracts the meta data from submitted POST.
     * 
     * @param integer $postId The ID of the post (schedule).
     * @return array The extracted meta data as an associative array of key => value pairs.
     */
    public function extractMeta($postId) {
        // Filter input post data
        $availabilityId = filter_input(INPUT_POST, 'edd-bk-schedule-availability-id', FILTER_SANITIZE_STRING);
        // Generate meta
        $meta = array(
                'availability_id'  =>  $availabilityId
        );
        if ($meta['availability_id'] === 'new') {
            $scheduleName = get_the_title($postId);
            $availabilityName = sprintf(__('Availability for %s', 'eddbk'), $scheduleName);
            $availabilityId = $this->getPlugin()->getAvailabilityController()->insert(array(
                    'post_title'    =>  $availabilityName
            ));
            $meta['availability_id'] = $availabilityId;
        }
        // Filter and return
        $filtered = \apply_filters('edd_bk_schedule_saved_meta', $meta);
        return $filtered;
    }
    
    /**
     * Sets the properties to their default.
     * 
     * @return SchedulePostType This instance.
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
        $filtered = \apply_filters('edd_bk_schedule_cpt_properties', $properties);
        $this->setProperties($filtered);
        return $this;
    }

    /**
     * Filters the row actions for the Schedule CPT.
     *
     * @param array $actions The row actions to filter.
     * @param \WP_Post $post The post for which the row actions will be filtered.
     * @return array The filtered row actions.
     */
    public function filterRowActions($actions, $post)
    {
        // If post type is our schedule cpt
        if ($post->post_type === $this->getSlug()) {
            // Remove the quickedit
            unset($actions['inline hide-if-no-js']);
        }
        return $actions;
    }
    
    /**
     * Filters the bulk actions for the Schedule CPT.
     * 
     * @param array $actions The bulk actions to filter.
     * @return array The filtered bulk actions.
     */
    public function filterBulkActions($actions)
    {
        unset($actions['edit']);
        return $actions;
    }
    
    /**
     * Registers the WordPress hooks.
     */
    public function hook()
    {
        $this->getPlugin()->getHookManager()
                ->addAction('init', $this, 'register', 11)
                ->addAction('add_meta_boxes', $this, 'addMetaboxes')
                ->addAction('save_post', $this, 'onSave', 10, 2)
                // Hooks for row actions
                ->addFilter('post_row_actions', $this, 'filterRowActions', 10, 2)
                // Hooks for removing bulk actions
                ->addFilter(sprintf('bulk_actions-edit-%s', $this->getSlug()), $this, 'filterBulkActions')
                // Filter updated notice message
                ->addFilter('post_updated_messages', $this, 'filterUpdatedMessages');
    }

}
