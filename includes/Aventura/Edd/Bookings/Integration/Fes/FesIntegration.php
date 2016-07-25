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

}
