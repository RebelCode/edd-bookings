<?php

namespace Aventura\Edd\Bookings\Controller;

use \Aventura\Edd\Bookings\CustomPostType\SchedulePostType;

/**
 * Controller class for schedules.
 * 
 * This class is responsible for retrieving stored instance, saving and updating creating instances and querying
 * the storage for instance of schedules.
 */
class ScheduleController extends ModelCptControllerAbstract
{

    /**
     * Gets the schedule with the given ID.
     * 
     * @param integer $id The ID of the schedule.
     * @return Schedule
     */
    public function get($id)
    {
        if (\get_post_type($id) !== $this->getPostType()->getSlug()) {
            $schedule = null;
        } else {
            // Get all custom meta fields
            $meta = $this->getMeta($id);
            // Generate data array
            $data = $meta;
            $data['id'] = $id;
            // Create the availability
            $schedule = $this->getFactory()->create($data);
        }
        return $schedule;
    }

    /**
     * Gets the schedules from the DB.
     * 
     * This is a generic query method that gets all schedules by default, but becomes more specific when using the
     * parameter.
     * 
     * @param array $metaQueries Optional, default: array(). An array of meta queries.
     * @return array All the saved schedules, or the schedules that match the given meta queries.
     */
    public function query(array $metaQueries = array())
    {
        $args = array(
                'post_type'   => SchedulePostType::SLUG,
                'post_status' => 'publish',
                'meta_query'  => $metaQueries
        );
        $filtered = \apply_filters('edd_bk_query_schedules', $args);
        // Submit query and compile array of schedules
        $query = new \WP_Query($filtered);
        $schedules = array();
        while ($query->have_posts()) {
            $query->the_post();
            $schedules[] = $this->get($query->post->ID);
        }
        // Reset WordPress' query data and return array
        $query->reset_postdata();
        return $schedules;
    }

    /**
     * Gets the schedules that use a specific availability.
     * 
     * @param integer $id The availability ID.
     * @return Schedule[] An array of Schedule instances.
     */
    public function getSchedulesForAvailability($id)
    {
        $metaQueries = array();
        $metaQueries[] = array(
                'key'     => 'availability_id',
                'value'   => $id,
                'compare' => '='
        );
        return $this->query($metaQueries);
    }

    /**
     * Registers the WordPress hooks.
     */
    public function hook()
    {
        $this->getPostType()->hook();
    }

    /**
     * {@inheritdoc}
     */
    public function insert(array $data = array(), $wp_error = false)
    {
        $default = array(
                'post_title'   => __('New schedule', $this->getPlugin()->getI18n()->getDomain()),
                'post_content' => '',
                'post_type'    => $this->getPostType()->getSlug(),
                'post_status'  => 'publish'
        );
        $args = \wp_parse_args($data, $default);
        $filteredArgs = \apply_filters('edd_bk_new_schedule_args', $args);
        $insertedId = parent::insert($filteredArgs, $wp_error);
        return \is_wp_error($insertedId)
                ? null
                : $insertedId;
    }

    /**
     * {@inheritdoc}
     */
    public function saveMeta($id, array $data = array())
    {
        \update_post_meta($id, 'availability_id', $data['availability_id']);
    }

}
