<?php

namespace Aventura\Edd\Bookings\Integration\Fes;

use \Aventura\Edd\Bookings\Integration\Core\IntegrationAbstract;

/**
 * Integration for the FrontEnd Submissions extension.
 *
 * @author Miguel Muscat <miguelmuscat93@gmail.com>
 */
class FesIntegration extends IntegrationAbstract
{

    /**
     * {@inheritdoc}
     */
    public function hook()
    {
        $this->getPlugin()->getHookManager()
            ->addAction('fes_load_fields_require', $this, 'init');
    }

    /**
     * Initializes the integration.
     * 
     * Checks for FES extension and its version to decide whether or not to proceed with integration.
     */
    public function init()
    {
        if (!class_exists('EDD_Front_End_Submissions')) {
            return;
        }
        if (!static::isFesLoaded()) {
            return;
        }
        add_filter('fes_load_fields_array', array($this, 'registerFields'));
    }

    /**
     * Filters the FES fields to register out custom fields.
     * 
     * The given fields array is an associative array of field ID and classname pairs.
     * 
     * @param array $fields The input fields.
     * @return The output fields.
     */
    public function registerFields($fields)
    {
        $classes = apply_filters('edd_bk_fes_fields', $this->getFieldClasses());
        return array_merge($fields, $classes);
    }

    /**
     * Gets the classes for the FES Fields.
     * 
     * @return array An array of field ID and fully qualified class name pairs.
     */
    public function getFieldClasses()
    {
        return array(
            'edd_bk' => 'Aventura\\Edd\\Bookings\\Integration\\Fes\\Field\\BookingsField'
        );
    }

    /**
     * Checks if the FES plugin is loaded and is at least at the required version.
     * 
     * @return boolean True if FES is loaded and is at least at the required version, false otherwise.
     */
    public static function isFesLoaded()
    {
        return defined('fes_plugin_version') && version_compare(fes_plugin_version, 2.3, '>=');
    }

    /**
     * Checks if the current page is an FES admin page.
     * 
     * @return boolean
     */
    public static function isFesAdminPage()
    {
        if (!fes_is_admin()) {
            return false;
        }

        $screen = \get_current_screen();
        if (!is_object($screen)) {
            return false;
        }

        $postTypes = array('fes-forms', 'download');
        $screens = array('profile', 'user-edit', 'user', 'user-new');

        return (isset($screen->base) && substr($screen->base, 0, 7) === 'edd-fes') ||
            (isset($screen->post_type) && in_array($screen->post_type, $postTypes)) ||
            (isset($screen->id) && in_array($screen->id, $screens));
    }

    /**
     * Gets the FES frontend page ID.
     * 
     * @return string
     */
    public static function getFesFrontendPage()
    {
        return EDD_FES()->helper->get_option('fes-vendor-dashboard-page', false);
    }

    /**
     * Checks if the current page is an FES frontend page.
     * 
     * @return boolean
     */
    public static function isFesFrontendPage()
    {
        return fes_is_frontend() && is_page(static::getFesFrontendPage());
    }

}
