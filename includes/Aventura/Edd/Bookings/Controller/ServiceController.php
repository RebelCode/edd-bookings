<?php

namespace Aventura\Edd\Bookings\Controller;

use \Aventura\Edd\Bookings\CustomPostType\ServicePostType;
use \Aventura\Edd\Bookings\Factory\ModelCptFactoryAbstract;
use \Aventura\Edd\Bookings\Plugin;
use \Aventura\Edd\Bookings\Model\Service;

/**
 * Description of ServiceController
 *
 * @author Miguel Muscat <miguelmuscat93@gmail.com>
 */
class ServiceController extends ControllerAbstract
{
    
    /**
     * Constructs a new instance.
     * 
     * @param Plugin $plugin The parent plugin instance.
     */
    public function __construct(Plugin $plugin, ModelCptFactoryAbstract $factory)
    {
        parent::__construct($plugin, $factory);
    }
    
    /**
     * Gets the booking CPT.
     * 
     * @return ServicePostType The booking CPT instance.
     */
    public function getPostType()
    {
        if (is_null($this->_cpt)) {
            $this->_cpt = $this->getFactory()->createCpt();
        }
        return $this->_cpt;
    }
    
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
            $meta = \get_post_custom($id);
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
            'post_type' => ServicePostType::SLUG,
            'post_status' => 'publish',
            'meta_query' => $query
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
    
}
