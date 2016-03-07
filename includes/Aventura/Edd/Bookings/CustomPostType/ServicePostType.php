<?php

namespace Aventura\Edd\Bookings\CustomPostType;

use \Aventura\Edd\Bookings\CustomPostType;
use \Aventura\Edd\Bookings\Plugin;
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
     * Regsiters the WordPress hooks.
     */
    public function hook()
    {
        $this->getPlugin()->getHookManager()
                ->addAction('add_meta_boxes', $this, 'addMetaboxes');
    }

}
