<?php

namespace Aventura\Edd\Bookings\Controller;

use \Aventura\Edd\Bookings\CustomPostType\ServicePostType;
use \Aventura\Edd\Bookings\Factory\ServiceFactory;
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
     * Registers the WordPress hooks.
     */
    public function hook()
    {
        $this->getPostType()->hook();
        $this->getPlugin()->getHookManager()
            ->addFilter('edd_bk_service_meta', $this, 'sanitizeMetaData');
        $this->getPlugin()->getAssetsController()->nq($this, 'enqueueAssets');
    }

    /**
     * Enqeueus the assets.
     *
     * @param array $assets
     * @param string $ctx
     * @param AssetsController $c
     * @return array
     */
    public function enqueueAssets(array $assets, $ctx, AssetsController $c)
    {
        switch ($ctx) {
            case AssetsController::CONTEXT_BACKEND:
                return $this->enqueueBackendAssets($assets, $c);
            case AssetsController::CONTEXT_FRONTEND:
                return $this->enqueueFrontendAssets($assets, $c);
            default:
                return $assets;
        }
    }

    /**
     * Enqueues the backend assets.
     *
     * @param array $assets The assets
     * @param AssetsController $c The assets controller instance.
     * @return array
     */
    protected function enqueueBackendAssets(array $assets, AssetsController $c) {
        $screen = get_current_screen();
        // Download pages only
        if ($screen->post_type !== $this->getPostType()->getSlug()) {
            return $assets;
        }
        if ($screen->id === 'download' || $screen->id === 'edit-download') {
            $assets = array_merge($assets, array(
                'eddbk.js.service.edit',
                'eddbk.css.service.edit',
                'eddbk.css.tooltips',
                'eddbk.js.availability.builder',
                'eddbk.css.availability.builder',
                'jquery-ui-datepicker',
                'jquery-ui-timepicker',
                'jquery-ui-timepicker-css'
            ));
        }
        return $assets;
    }

    /**
     * Enqueues the frontend assets.
     *
     * @param array $assets The assets
     * @param AssetsController $c The assets controller instance.
     * @return array
     */
    protected function enqueueFrontendAssets(array $assets, AssetsController $c) {
        if (is_single() && get_post_type() === $this->getPostType()->getSlug()) {
            $assets = array_merge($assets, array(
                'eddbk.js.service.frontend',
                'eddbk.css.service.frontend',
                'jquery-ui-datepicker'
            ));
        }
        return $assets;
    }

    /**
     * Sanitizes meta data.
     *
     * Whilst the factory handles the default values, this ensures that the meta data is valid.
     * For instance, the min and max number of sessions default to 1 in the factory if not given. On the other hand, this method
     * ensures that these values are not less than 1.
     *
     * @param array $meta The input array of meta data.
     * @return array The output array of meta data.
     */
    public function sanitizeMetaData(array $meta)
    {
        $meta['min_sessions'] = max(1, intval($meta['min_sessions']));
        $meta['max_sessions'] = max(1, intval($meta['max_sessions']));

        return $meta;
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
     * @param array $metaQuery Optional query. Default: array()
     * @return array An array of services that matched the query.
     */
    public function query(array $metaQuery = array())
    {
        $args = array(
                'post_type'   => ServicePostType::SLUG,
                'post_status' => 'publish',
                'meta_query'  => $metaQuery
        );
        $filtered = \apply_filters('edd_bk_query_services', $args);
        // Submit query and compile array of services
        $query = $this->_createQuery($filtered);
        $services = array();
        while ($query->have_posts()) {
            $query->the_post();
            $services[] = $this->get(\get_the_ID());
        }
        // Reset WordPress' query data and return array
        $this->_resetQuery();
        return $services;
    }

    /**
     * Queries the DB for services that use a specific availability.
     *
     * @param integer|array $id The availability ID, or an array of availability IDs.
     * @return array An array of Availability instances.
     */
    public function getServicesForAvailability($id)
    {
        // Prepare query args
        $metaQueries = array();
        $metaQueries[] = array(
                'key'     => $this->metaPrefix('availability_id'),
                'value'   => $id,
                'compare' => (is_array($id) ? 'IN' : '=')
        );
        $filtered = \apply_filters('edd_bk_query_services_for_availability', $metaQueries, $id);
        return $this->query($filtered);
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
        $merged = $this->getFactory()->normalizeMeta($final);
        return \apply_filters('edd_bk_get_service_meta', $merged);
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
