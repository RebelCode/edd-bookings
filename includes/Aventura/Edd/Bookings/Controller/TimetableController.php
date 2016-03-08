<?php

namespace Aventura\Edd\Bookings\Controller;

use \Aventura\Edd\Bookings\CustomPostType\TimetablePostType;

/**
 * Description of TimetableController
 *
 * @author Miguel Muscat <miguelmuscat93@gmail.com>
 */
class TimetableController extends ModelCptControllerAbstract
{
    
    /**
     * Gets a timetable by ID.
     * 
     * @param type $id
     */
    public function get($id)
    {
        if (\get_post_type($id) !== TimetablePostType::SLUG) {
            $timetable = null;
        } else {
            // Get all custom meta fields
            $meta = $this->getMeta($id);
            // Generate data array
            $data = $meta;
            $data['id'] = $id;
            // Create the timetable
            $timetable = $this->getFactory()->create($data);
        }
        return $timetable;
    }

    /**
     * Gets the timetables from the DB.
     * 
     * This is a generic query method that gets all timetables by default, but becomes more specific when using the
     * parameter.
     * 
     * @param array $metaQueries Optional, default: array(). An array of meta queries.
     * @return array All the saved timetables, or the timetables that match the given meta queries.
     */
    public function query(array $metaQueries = array())
    {
        $args = array(
            'post_type' => TimetablePostType::SLUG,
            'post_status' => 'publish',
            'meta_query' => $metaQueries
        );
        $filtered = \apply_filters('edd_bk_query_timetables', $args);
        // Submit query and compile array of timetables
        $query = new \WP_Query($filtered);
        $timetables = array();
        while ($query->have_posts()) {
            $query->the_post();
            $timetables[] = $this->get(\get_the_ID());
        }
        // Reset WordPress' query data and return array
        \wp_reset_postdata();
        return $timetables;
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
        $default = array(
                'post_title'   => __('New timetable', $this->getPlugin()->getI18n()->getDomain()),
                'post_content' => '',
                'post_type'    => $this->getPostType()->getSlug(),
                'post_status'  => 'publish'
        );
        $args = \wp_parse_args($data, $default);
        $filteredArgs = \apply_filters('edd_bk_new_timetable_args', $args);
        $insertedId = \wp_insert_post($filteredArgs);
        return \is_wp_error($insertedId)
                ? null
                : $insertedId;
    }
    
    /**
     * {@inheritdoc}
     */
    public function saveMeta($id, array $data = array())
    {
        \update_post_meta($id, 'rules', $data['rules']);
    }

}
