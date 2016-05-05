<?php

namespace Aventura\Edd\Bookings\CustomPostType;

use \Aventura\Edd\Bookings\CustomPostType;
use \Aventura\Edd\Bookings\Plugin;
use \Aventura\Edd\Bookings\Renderer\AvailabilityRenderer;
use \Aventura\Edd\Bookings\Availability\Rule\Renderer\RuleRendererAbstract;

/**
 * The Availability Custom Post Type.
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
    public function __construct($plugin)
    {
        parent::__construct($plugin, static::SLUG);
        $this->generateLabels(__('Availability', 'eddbk'), __('Availabilities', 'eddbk'))
                ->setLabel('all_items', __('Availabilities', 'eddbk'))
                ->setDefaultProperties();
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
                'show_in_menu' => 'edd-bookings',
                'supports'     => array('title')
        );
        $filtered = \apply_filters('edd_bk_availability_cpt_properties', $properties);
        $this->setProperties($filtered);
        return $this;
    }

    /**
     * Registers the metaboxes.
     */
    public function addMetaboxes()
    {
        global $post;
        $textDomain = $this->getPlugin()->getI18n()->getDomain();
        $metaboxArgs = compact('post');
        $screen = \get_current_screen();
        // Rules metabox
        \add_meta_box('edd-bk-rules', __('Rules', $textDomain), array($this, 'renderRulesMetabox'), static::SLUG,
                'normal', 'core', $metaboxArgs);
        // Preview metabox
        /*
        \add_meta_box('edd-bk-availability-preview', __('Preview', $textDomain), array($this, 'renderPreviewMetabox'),
                static::SLUG, 'side', 'core', $metaboxArgs);
         */
        // Schedules using this availability metabox
        if ($screen->action !== 'add') {
            \add_meta_box('edd-bk-availability-schedules', __('Schedules using this availability', $textDomain),
                    array($this, 'renderSchedulesMetabox'), static::SLUG, 'normal', 'low', $metaboxArgs);
        }
    }

    /**
     * Renders the rules metabox.
     */
    public function renderRulesMetabox($currentPost, $metabox)
    {
        $post = $metabox['args']['post'];
        $availability = (empty($post->ID))
                ? $this->getPlugin()->getAvailabilityController()->getFactory()->create(array('id' => 0))
                : $this->getPlugin()->getAvailabilityController()->get($post->ID);
        $renderer = new AvailabilityRenderer($availability);
        echo $renderer->render();
    }

    /**
     * Renders the preview metabox.
     */
    public function renderPreviewMetabox($currentPost, $metabox)
    {
        $post = $metabox['args']['post'];
        $availability = (empty($post->ID))
                ? $this->getPlugin()->getAvailabilityController()->getFactory()->create(array('id' => 0))
                : $this->getPlugin()->getAvailabilityController()->get($post->ID);
        $renderer = new AvailabilityRenderer($availability);
        echo $renderer->renderPreview();
    }
    
    /**
     * Renders the services metabox.
     */
    public function renderSchedulesMetabox($currentPost, $metabox)
    {
        $post = $metabox['args']['post'];
        $schedules = $this->getPlugin()->getScheduleController()->getSchedulesForAvailability($post->ID);
        if (count($schedules) > 0) {
            foreach ($schedules as $schedule) {
                /* @var $schedule \Aventura\Edd\Bookings\Model\Schedule */
                $availId = $schedule->getId();
                $link = sprintf(\admin_url('post.php?post=%s&action=edit'), $availId);
                $name = \get_the_title($availId);
                printf('<p><strong><a href="%s">%s</a></strong></p>', $link, $name);
            }
        } else {
            echo __('There are no schedules using this availability', 'eddbk');
        }
    }
    
    /**
     * Callback triggered when an availability is saved or updated.
     * 
     * @param integer $postId The availability post ID.
     * @param WP_Post $post The availability post object.
     */
    public function onSave($postId, $post)
    {
        if ($this->_guardOnSave($postId, $post)) {
            // verify nonce
            \check_admin_referer('edd_bk_save_meta', 'edd_bk_availability');
            // Save the download meta
            $meta = $this->extractMeta();
            $this->getPlugin()->getAvailabilityController()->saveMeta($postId, $meta);
        }
    }
    
    /**
     * Extracts the meta data from submitted POST.
     * 
     * @return array The extracted meta data as an associative array of key => value pairs.
     */
    public function extractMeta() {
        // Filter input post data
        $ruleTypes = filter_input(INPUT_POST, 'edd-bk-rule-type', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
        $ruleStarts = filter_input(INPUT_POST, 'edd-bk-rule-start', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
        $ruleEnds = filter_input(INPUT_POST, 'edd-bk-rule-end', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
        $ruleAvailables = filter_input(INPUT_POST, 'edd-bk-rule-available', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
        // Compile rules
        $rules = array();
        for($i = 0; $i < count($ruleTypes); $i++) {
            $rules[] = array(
                    'type' => str_replace('\\', '\\\\', $ruleTypes[$i]),
                    'start' => $ruleStarts[$i],
                    'end' => $ruleEnds[$i],
                    'available' => $ruleAvailables[$i],
            );
        }
        // Prepare meta array
        $meta = array(
                'rules' => $rules
        );
        // Filter and return
        $filtered = \apply_filters('edd_bk_availability_saved_meta', $meta);
        return $filtered;
    }

    /**
     * Handles AJAX request for UI rows.
     */
    public function handleAjaxRowRequest()
    {
        \check_admin_referer('edd_bk_availability_ajax', 'edd_bk_availability_ajax_nonce');
        if (!\current_user_can('manage_options')) {
            die;
        }
        $error = 0;
        $rendered = '';
        $ruleType = filter_input(INPUT_POST, 'ruletype', FILTER_SANITIZE_STRING);
        if ($ruleType === false) {
            $error = 1;
        } elseif (empty($ruleType)) {
            $rendered = AvailabilityRenderer::renderRule(null);
        } else {
            $rendererClass = AvailabilityRenderer::getRuleRendererClassName($ruleType);
            /* @var $renderer RuleRendererAbstract */
            $renderer = $rendererClass::getDefault();
            // Generate rendered output
            $start = $renderer->renderRangeStart();
            $end = $renderer->renderRangeEnd();
            $rendered = compact('start', 'end');
        }
        $response = array(
                'error'    => $error,
                'rendered' => $rendered
        );
        $filteredResponse = \apply_filters('edd_bk_tedd_bk_availability_ajax_row_render', $response);
        echo json_encode($filteredResponse);
        die();
    }

    /**
     * Filters the row actions for the Availability CPT.
     *
     * @param array $actions The row actions to filter.
     * @param \WP_Post $post The post for which the row actions will be filtered.
     * @return array The filtered row actions.
     */
    public function filterRowActions($actions, $post)
    {
        // If post type is our schedule cpt
        if ($post->post_type === $this->getSlug()) {
            // Remove the quickedit
            unset($actions['inline hide-if-no-js']);
        }
        return $actions;
    }
    
    /**
     * Filters the bulk actions for the Availability CPT.
     * 
     * @param array $actions The bulk actions to filter.
     * @return array The filtered bulk actions.
     */
    public function filterBulkActions($actions)
    {
        unset($actions['edit']);
        return $actions;
    }
    
    /**
     * Registers the WordPress hooks.
     */
    public function hook()
    {
        $this->getPlugin()->getHookManager()
                ->addAction('init', $this, 'register', 12)
                ->addAction('save_post', $this, 'onSave', 10, 2)
                ->addAction('add_meta_boxes', $this, 'addMetaboxes')
                ->addAction('wp_ajax_get_row_render', $this, 'handleAjaxRowRequest')
                // Hooks for row actions
                ->addFilter('post_row_actions', $this, 'filterRowActions', 10, 2)
                // Hooks for removing bulk actions
                ->addFilter(sprintf('bulk_actions-edit-%s', $this->getSlug()), $this, 'filterBulkActions')
                // Filter updated notice message
                ->addFilter('post_updated_messages', $this, 'filterUpdatedMessages');
    }

}
