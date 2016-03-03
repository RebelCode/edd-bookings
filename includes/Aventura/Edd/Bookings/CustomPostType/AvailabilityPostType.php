<?php

namespace Aventura\Edd\Bookings\CustomPostType;

use \Aventura\Edd\Bookings\CustomPostType;
use \Aventura\Edd\Bookings\Plugin;

/**
 * The Availability custom post type.
 *
 * @author Miguel Muscat <miguelmuscat93@gmail.com>
 */
class AvailabilityPostType extends CustomPostType
{
    
    /**
     * The CPT slug name.
     */
    const SLUG = 'edd_bk_availability';
    
    /**
     * Constructs a new instance.
     * 
     * @param Plugin $plugin The parent plugin instance.
     */
    public function __construct(Plugin $plugin)
    {
        parent::__construct($plugin, self::SLUG);
        $this->generateLabels('Availability', 'Availabilities');
        $this->setDefaultProperties();
    }

    /**
     * Sets the properties to their default.
     * 
     * @return AvailabilityPostType This instance.
     */
    public function setDefaultProperties()
    {
        $properties = array(
            'public' => false,
            'show_ui' => true,
            'has_archive' => false,
            'show_in_menu' => 'edit.php?post_type=download',
            'supports' => array('title')
        );
        $filtered = \apply_filters('edd_bk_availability_cpt_properties', $properties);
        $this->setProperties($filtered);
        return $this;
    }
    
    /**
     * Registers the WordPress hooks.
     */
    public function hook()
    {
        $this->getPlugin()->getHookManager()
                ->addAction('init', $this, 'register');
    }

}
