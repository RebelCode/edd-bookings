<?php

namespace Aventura\Edd\Bookings\CustomPostType;

use \Aventura\Edd\Bookings\CustomPostType;
use \Aventura\Edd\Bookings\Plugin;
use \Aventura\Edd\Bookings\Renderer\TimetableRenderer;
use \Aventura\Edd\Bookings\Timetable\Rule\Renderer\RuleRendererAbstract;

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
                'public'       => false,
                'show_ui'      => true,
                'has_archive'  => false,
                'show_in_menu' => 'edit.php?post_type=download',
                'supports'     => array('title')
        );
        $filtered = \apply_filters('edd_bk_timetable_cpt_properties', $properties);
        $this->setProperties($filtered);
        return $this;
    }

    /**
     * Registers the metaboxes.
     */
    public function addMetaboxes()
    {
        \add_meta_box('edd-bk-rules', 'Rules', array($this, 'renderRulesMetabox'), static::SLUG, 'normal', 'core');
    }

    /**
     * Renders the rules metabox.
     */
    public function renderRulesMetabox($post)
    {
        $timetable = (empty($post->ID))
                ? $this->getPlugin()->getTimetableController()->getFactory()->create(array('id' => 0))
                : $this->getPlugin()->getTimetableController()->get($post->ID);
        $renderer = new TimetableRenderer($timetable);
        echo $renderer->render();
    }

    /**
    
    /**
     * Callback triggered when a timetable is saved or updated.
     * 
     * @param integer $postId The timetable post ID.
     * @param WP_Post $post The timetable post object.
     */
    public function onSave($postId, $post)
    {
        if ($this->_guardOnSave($postId, $post)) {
            // verify nonce
            \check_admin_referer('edd_bk_save_meta', 'edd_bk_timetable');
            // Save the download meta
            $meta = $this->extractMeta();
            $this->getPlugin()->getTimetableController()->saveMeta($postId, $meta);
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
        $filtered = \apply_filters('edd_bk_timetable_saved_meta', $meta);
        return $filtered;
    }

    /**
     * Handles AJAX request for UI rows.
     */
    public function handleAjaxRowRequest()
    {
        $error = 0;
        $rendered = '';
        $ruleType = filter_input(INPUT_POST, 'ruletype', FILTER_SANITIZE_STRING);
        if ($ruleType === false) {
            $error = 1;
        } elseif (empty($ruleType)) {
            $rendered = TimetableRenderer::renderRule(null);
        } else {
            $rendererClass = TimetableRenderer::getRuleRendererClassName($ruleType);
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
        $filteredResponse = \apply_filters('edd_bk_timetable_ajax_row_render', $response);
        echo json_encode($filteredResponse);
        die();
    }

    /**
     * Registers the WordPress hooks.
     */
    public function hook()
    {
        $this->getPlugin()->getHookManager()
                ->addAction('init', $this, 'register')
                ->addAction('save_post', $this, 'onSave', 10, 2)
                ->addAction('add_meta_boxes', $this, 'addMetaboxes')
                ->addAction('wp_ajax_get_row_render', $this, 'handleAjaxRowRequest');
    }

}
