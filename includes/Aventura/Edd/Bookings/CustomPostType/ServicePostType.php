<?php

namespace Aventura\Edd\Bookings\CustomPostType;

use \Aventura\Diary\DateTime\Duration;
use \Aventura\Edd\Bookings\CustomPostType;
use \Aventura\Edd\Bookings\Plugin;
use \Aventura\Edd\Bookings\Renderer\FrontendRenderer;
use \Aventura\Edd\Bookings\Renderer\ServiceRenderer;

/**
 * Service Custom Post Type class.
 *
 * @author Miguel Muscat <miguelmuscat93@gmail.com>
 */
class ServicePostType extends CustomPostType
{

    /**
     * The CPT slug name.
     */
    const SLUG = 'download';

    /**
     * Constructs a new instance.
     * 
     * @param Plugin $plugin The parent plugin instance.
     */
    public function __construct(Plugin $plugin)
    {
        parent::__construct($plugin, self::SLUG);
    }

    /**
     * Registers the WordPress metaboxes for this cpt.
     */
    public function addMetaBoxes()
    {
        \add_meta_box('edd-bk-service', __('Booking Options', $this->getPlugin()->getI18n()->getDomain()),
                array($this, 'renderServiceMetabox'), static::SLUG, 'normal', 'high');
    }

    /**
     * Renders the service metabox.
     * 
     * @param WP_Post $post The post.
     */
    public function renderServiceMetabox($post)
    {
        $service = (empty($post->ID))
                ? $this->getPlugin()->getServiceController()->getFactory()->create(array('id' => 0))
                : $this->getPlugin()->getServiceController()->get($post->ID);
        $renderer = new ServiceRenderer($service);
        echo $renderer->render();
    }

    /**
     * Renders a service on the frontend.
     * 
     * @param integer $id The ID of the service.
     * @param array $args Optional array of arguments. Default: array()
     */
    public function renderServiceFrontend($id, $args = array())
    {
        $service = $this->getPlugin()->getServiceController()->get($id);
        $renderer = new FrontendRenderer($service);
        echo $renderer->render();
    }
    
    /**
     * Called when a service is saved.
     * 
     * @param integer $postId The post ID
     * @param WP_Post $post The post object
     */
    public function onSave($postId, $post)
    {
        if ($this->_guardOnSave($postId, $post)) {
            // verify nonce
            \check_admin_referer('edd_bk_save_meta', 'edd_bk_service');
            // Get the meta from the POST data
            $meta = $this->extractMeta();
            $this->getPlugin()->getServiceController()->saveMeta($postId, $meta);
        }
    }

    /**
     * Extracts the meta data from submitted POST data.
     * 
     * @return array The extracted meta data
     */
    public function extractMeta()
    {
        // Prepare meta array
        $meta = array(
                'bookings_enabled'  => filter_input(INPUT_POST, 'edd-bk-bookings-enabled', FILTER_VALIDATE_BOOLEAN),
                'session_length'    => filter_input(INPUT_POST, 'edd-bk-session-length', FILTER_SANITIZE_NUMBER_INT),
                'session_unit'      => filter_input(INPUT_POST, 'edd-bk-session-unit', FILTER_SANITIZE_STRING),
                'session_cost'      => filter_input(INPUT_POST, 'edd-bk-session-cost', FILTER_VALIDATE_FLOAT),
                'min_sessions'      => filter_input(INPUT_POST, 'edd-bk-min-sessions', FILTER_SANITIZE_NUMBER_INT),
                'max_sessions'      => filter_input(INPUT_POST, 'edd-bk-max-sessions', FILTER_SANITIZE_NUMBER_INT),
                'multi_view_output' => filter_input(INPUT_POST, 'edd-bk-multiview-output', FILTER_VALIDATE_BOOLEAN),
                'availability_id'   => filter_input(INPUT_POST, 'edd-bk-service-availability', FILTER_SANITIZE_NUMBER_INT),
        );
        // Convert session length into seconds, based on the unit
        $sessionUnit = $meta['session_unit'];
        $meta['session_length'] = Duration::$sessionUnit(1, false) * ($meta['session_length']);
        // Filter and return
        $filtered = \apply_filters('edd_bk_service_saved_meta', $meta);
        return $filtered;
    }

    /**
     * Regsiters the WordPress hooks.
     */
    public function hook()
    {
        $this->getPlugin()->getHookManager()
                ->addAction('add_meta_boxes', $this, 'addMetaboxes')
                ->addAction('save_post', $this, 'onSave', 10, 2)
                ->addAction('edd_purchase_link_top', $this, 'renderServiceFrontend', 10, 2 );
    }

}
