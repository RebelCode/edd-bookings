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
        $query = new \WP_Query($filtered);
        $services = array();
        while ($query->have_posts()) {
            $query->the_post();
            $services[] = $this->get(\get_the_ID());
        }
        // Reset WordPress' query data and return array
        $query->reset_postdata();
        return $services;
    }

    /**
     * Queries the DB for services that use a specific availability.
     * 
     * @param integer|array $id The availability ID, or an array of availability IDs.
     * @return array An array of Service instances.
     */
    public function getServicesForAvailability($id)
    {
        // Prepare query args
        $metaQueries = array();
        $metaQueries[] = array(
                'key'     => 'availability_id',
                'value'   => $id,
                'compare' => (is_array($id) ? 'IN' : '=')
        );
        $filtered = \apply_filters('edd_bk_query_services_for_availability', $metaQueries, $id);
        return $this->query($filtered);
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
        // Do nothing. This is an EDD CPT
    }

    /**
     * {@inheritdoc}
     */
    public function getMeta($id, array $data = array())
    {
        $meta = parent::getMeta($id);
        $legacy = isset($meta['edd_bk'])? $meta['edd_bk'] : null;
        $final = array();
        // If meta found, set final to meta
        if (is_array($meta)) {
            $final = $meta;
        } elseif (is_array($legacy)) {
            // Otherwise, add legacy meta if found
            $final['legacy'] = $legacy;
        }
        return \apply_filters('edd_bk_get_service_meta', $final);
    }

    /**
     * {@inheritdoc}
     */
    public function saveMeta($id, array $data = array())
    {
        unset($data['id']);
        $filtered = \apply_filters('edd_bk_save_service_meta', $data, $id);
        foreach ($data as $key => $value) {
            \update_post_meta($id, $key, $value);
        }
    }

}
