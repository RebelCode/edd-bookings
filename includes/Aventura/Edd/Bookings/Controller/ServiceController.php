<?php

namespace Aventura\Edd\Bookings\Controller;

use \Aventura\Edd\Bookings\CustomPostType\ServicePostType;
use \Aventura\Edd\Bookings\Model\Service;

/**
 * Description of ServiceController
 *
 * @author Miguel Muscat <miguelmuscat93@gmail.com>
 */
class ServiceController extends ModelCptControllerAbstract
{

    /**
     * Gets a single service by ID.
     * 
     * @param integer $id The ID.
     * @return Service The service with the given ID, or null if it doesn't exist.
     */
    public function get($id)
    {
        if (\get_post_type($id) !== ServicePostType::SLUG) {
            $service = null;
        } else {
            // Get all custom meta fields
            $meta = $this->getMeta($id);
            // Add the ID
            $meta['id'] = $id;
            // Create the service
            $service = $this->getFactory()->create($meta);
        }
        return $service;
    }

    /**
     * {@inheritdoc}
     * 
     * @param array $query Optional query. Default: array()
     * @return array An array of services that matched the query.
     */
    public function query(array $query = array())
    {
        $args = array(
                'post_type'   => ServicePostType::SLUG,
                'post_status' => 'publish',
                'meta_query'  => $query
        );
        $filtered = \apply_filters('edd_bk_query_services', $args);
        // Submit query and compile array of services
        $results = new \WP_Query($filtered);
        $services = array();
        while ($results->have_posts()) {
            $results->the_post();
            $services[] = $this->get(\get_the_ID());
        }
        // Reset WordPress' query data and return array
        \wp_reset_postdata();
        return $services;
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
    public function insert(array $data = array())
    {
        // Do nothing. This is an EDD CPT
    }

    /**
     * {@inheritdoc}
     */
    public function getMeta($id, array $data = array())
    {
        return \get_post_meta($id, 'edd_bk_service', true);
    }

    /**
     * {@inheritdoc}
     */
    public function saveMeta($id, array $data = array())
    {
        \update_post_meta($id, 'edd_bk_service', $data);
    }

}
