<?php

namespace Aventura\Edd\Bookings\CustomPostType;

use \Aventura\Edd\Bookings\CustomPostType;

/**
 * Description of TimetablePostType
 *
 * @author Miguel Muscat <miguelmuscat93@gmail.com>
 */
class TimetablePostType extends CustomPostType
{
    
    /**
     * The CPT slug name.
     */
    const SLUG = 'edd_bk_timetable';
    
    /**
     * Constructs a new instance.
     * 
     * @param Plugin $plugin The parent plugin instance.
     */
    public function __construct($plugin)
    {
        parent::__construct($plugin, static::SLUG);
        $this->generateLabels('Timetable', 'Timetables');
        $this->setDefaultProperties();
    }
    
    /**
     * Sets the properties to their default.
     * 
     * @return TimetablePostType This instance.
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
        $filtered = \apply_filters('edd_bk_timetable_cpt_properties', $properties);
        $this->setProperties($filtered);
        return $this;
    }
    
    public function hook()
    {
        $this->getPlugin()->getHookManager()
                ->addAction('init', $this, 'register');
    }

}
