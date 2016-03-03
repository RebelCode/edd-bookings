<?php

namespace Aventura\Edd\Bookings\Controller;

use \Aventura\Edd\Bookings\CustomPostType\AvailabilityPostType;
use \Aventura\Edd\Bookings\Service\Availability;

/**
 * Description of AvailabilityController
 *
 * @author Miguel Muscat <miguelmuscat93@gmail.com>
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
            $meta = \get_post_custom($id);
            // Add the ID
            $meta['id'] = $id;
            // Create the availability
            $availability = $this->getFactory()->create($meta);
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
            'post_type' => AvailabilityPostType::SLUG,
            'post_status' => 'publish',
            'meta_query' => $metaQueries
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

}
