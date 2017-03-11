<?php

namespace RebelCode\Storage\WordPress\Adapter;

/**
 * Common functionality for WordPress query adapters.
 *
 * @since [*next-version*]
 */
class AbstractQueryAdapter
{
    /**
     * Parameter-less constructor.
     * 
     * To be invoked in the actual constructor.
     * 
     * @since [*next-version*]
     */
    protected function _construct()
    {
    }

    /**
     * The columns for the posts table.
     *
     * Also represents the indexes accepted in a post data array.
     *
     * @since [*next-version*]
     *
     * @return array An array containing the string names for post columns.
     */
    protected function _getPostColumns()
    {
        return array(
            'ID',
            'post_author',
            'post_date',
            'post_date_gmt',
            'post_content',
            'post_content_filtered',
            'post_title',
            'post_excerpt',
            'post_status',
            'post_type',
            'comment_status',
            'ping_status',
            'post_password',
            'post_name',
            'to_ping',
            'pinged',
            'post_modified',
            'post_modified_gmt',
            'post_parent',
            'menu_order',
            'post_mime_type',
            'post_category',
            'tax_input',
            'meta_input',
            'guid',
            'import_id',
            'context',
        );
    }

    protected function _getBaseQueryFields()
    {

    }

    protected function _getTaxQueryFields()
    {
        
    }

    /**
     * Retrieves date fields of a query.
     * 
     * See {@link https://core.trac.wordpress.org/browser/tags/4.7.3/src/wp-includes/class-wp-query.php#L1836 date params in code}.
     *
     * @since [*next-version*]
     *
     * @return array An array of fields that relate to date attributes of a post query.
     */
    protected function _getDateQueryFields()
    {
        return array(
            'hour',
            'minute',
            'second',
            'year',
            'monthnum',
            'week',
            'day'
        );
    }
}
