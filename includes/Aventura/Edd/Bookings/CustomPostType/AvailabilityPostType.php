<?php

namespace Aventura\Edd\Bookings\CustomPostType;

use \Aventura\Edd\Bookings\CustomPostType;
use \Aventura\Edd\Bookings\Plugin;
use \Aventura\Edd\Bookings\Renderer\AvailabilityRenderer;

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
     * Registers the metaboxes.
     */
    public function addMetaboxes()
    {
        $textDomain = $this->getPlugin()->getI18n()->getDomain();
        \add_meta_box('edd-bk-availability-options', __('Options', $textDomain), array($this, 'renderOptionsMetabox'),
                static::SLUG, 'normal', 'core');
    }

    /**
     * Renders the metabox.
     */
    public function renderOptionsMetabox($post)
    {
        $availability = (empty($post->ID)) ? $this->getPlugin()->getAvailabilityController()->getFactory()->create(array(
                        'id' => 0)) : $this->getPlugin()->getAvailabilityController()->get($post->ID);
        $renderer = new AvailabilityRenderer($availability);
        echo $renderer->render();
    }

    public function onSave($postId, $post) {
        if ($this->_guardOnSave($postId, $post)) {
            check_admin_referer('edd_bk_save_meta', 'edd_bk_availability');
            // Save the download meta
            $meta = $this->extractMeta();
            $this->getPlugin()->getAvailabilityController()->saveMeta($postId, $meta);
        }
    }
    
    public function extractMeta() {
        // Filter input post data
        $timetableId = filter_input(INPUT_POST, 'edd-bk-availability-timetable-id', FILTER_VALIDATE_INT);
        // Generate meta
        $meta = array(
                'timetable_id'  =>  $timetableId
        );
        // Filter and return
        $filtered = \apply_filters('edd_bk_availability_saved_meta', $meta);
        return $filtered;
    }
    
    /**
     * Sets the properties to their default.
     * 
     * @return AvailabilityPostType This instance.
     */
    public function setDefaultProperties()
    {
        $properties = array(
                'public'       => false,
                'show_ui'      => true,
                'has_archive'  => false,
                'show_in_menu' => 'edit.php?post_type=download',
                'supports'     => array('title')
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
                ->addAction('init', $this, 'register')
                ->addAction('add_meta_boxes', $this, 'addMetaboxes')
                ->addAction('save_post', $this, 'onSave', 10, 2);
    }

}
