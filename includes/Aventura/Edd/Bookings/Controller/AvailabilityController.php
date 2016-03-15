<?php

namespace Aventura\Edd\Bookings\Controller;

use \Aventura\Edd\Bookings\CustomPostType\AvailabilityPostType;
use \Aventura\Edd\Bookings\Service\Availability;

/**
 * Controller class for availabilities.
 * 
 * This class is responsible for retrieving stored instance, saving and updating creating instances and querying
 * the storage for instance of availabilities.
 */
class AvailabilityController extends ModelCptControllerAbstract
{

    /**
     * Gets the availability with the given ID.
     * 
     * @param integer $id The ID of the availability.
     * @return Availability
     */
    public function get($id)
    {
        if (\get_post($id) === false) {
            $availability = null;
        } else {
            // Get all custom meta fields
            $meta = $this->getMeta($id);
            // Generate data array
            $data = $meta;
            $data['id'] = $id;
            // Create the timetable
            $availability = $this->getFactory()->create($data);
        }
        return $availability;
    }

    /**
     * Gets the availabilities from the DB.
     * 
     * This is a generic query method that gets all availabilities by default, but becomes more specific when using the
     * parameter.
     * 
     * @param array $metaQueries Optional, default: array(). An array of meta queries.
     * @return array All the saved availabilities, or the availabilities that match the given meta queries.
     */
    public function query(array $metaQueries = array())
    {
        $args = array(
                'post_type'   => AvailabilityPostType::SLUG,
                'post_status' => 'publish',
                'meta_query'  => $metaQueries
        );
        $filtered = \apply_filters('edd_bk_query_availabilities', $args);
        // Submit query and compile array of availabilities
        $query = new \WP_Query($filtered);
        $availabilities = array();
        while ($query->have_posts()) {
            $query->the_post();
            $availabilities[] = $this->get(\get_the_ID());
        }
        // Reset WordPress' query data and return array
        \wp_reset_postdata();
        return $availabilities;
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
                'post_title'   => __('New availability', $this->getPlugin()->getI18n()->getDomain()),
                'post_content' => '',
                'post_type'    => $this->getPostType()->getSlug(),
                'post_status'  => 'publish'
        );
        $args = \wp_parse_args($data, $default);
        $filteredArgs = \apply_filters('edd_bk_new_availability_args', $args);
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
        \update_post_meta($id, 'timetable_id', $data['timetable_id']);
    }

}
