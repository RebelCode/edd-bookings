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

    const META_PREFIX = 'edd_bk_';
    
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
     * Queries the DB for services that use a specific schedule.
     * 
     * @param integer|array $id The schedule ID, or an array of schedule IDs.
     * @return array An array of Service instances.
     */
    public function getServicesForSchedule($id)
    {
        // Prepare query args
        $metaQueries = array();
        $metaQueries[] = array(
                'key'     => $this->metaPrefix('schedule_id'),
                'value'   => $id,
                'compare' => (is_array($id) ? 'IN' : '=')
        );
        $filtered = \apply_filters('edd_bk_query_services_for_schedule', $metaQueries, $id);
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
        $allMeta = parent::getMeta($id);
        $legacy = isset($allMeta['edd_bk'])
                ? maybe_unserialize($allMeta['edd_bk'])
                : null;
        $final = array();
        // Get meta with prefix - run regex on all meta keys. preg_grep works on array values, so we use array_keys()
        // to generate a number-indexed array containing all key strings and work with that. Returned array will be
        // number-indexed as [i] => [key string]
        $metaKeys = preg_grep(sprintf('/^%s/', static::META_PREFIX), array_keys($allMeta));
        // To get the meta array, we use array_intersect_key(), which gets all elements with the same key in both param
        // arrays. Two arrays used are the allMeta array (assoc) and the flip of the preg_grep returned array.
        $meta = array_intersect_key($allMeta, array_flip($metaKeys));
        // If the meta array is populated ...
        if (is_array($meta) && count($meta) > 0) {
            // Generate the final array.
            // We now need to remove the prefixes so we can pass the proper data to the factory.
            $prefixLength = strlen(static::META_PREFIX);
            $finalKeys = array_map(function($item) use ($prefixLength) {
                return substr($item, $prefixLength);
            }, array_keys($meta));
            // Generate the final array
            $final = array_combine($finalKeys, array_values($meta));
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
            \update_post_meta($id, $this->metaPrefix($key), $value);
        }
    }

    /**
     * Prepends the meta prefix to the given meta key.
     * 
     * @param string $key The key.
     * @return string The key, prepended with the meta prefix.
     */
    public function metaPrefix($key)
    {
        return sprintf('%s%s', static::META_PREFIX, $key);
    }
    
}
