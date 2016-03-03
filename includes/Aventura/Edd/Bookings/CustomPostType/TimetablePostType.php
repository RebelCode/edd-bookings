<?php

namespace Aventura\Edd\Bookings\CustomPostType;

use \Aventura\Diary\Bookable\Availability\Timetable\Rule\DotwRangeRule;
use \Aventura\Diary\Bookable\Availability\Timetable\Rule\RangeRuleAbstract;
use \Aventura\Diary\Bookable\Availability\Timetable\Rule\WeekNumRangeRule;
use \Aventura\Edd\Bookings\CustomPostType;
use \Aventura\Edd\Bookings\Model\Timetable;
use \Aventura\Edd\Bookings\Plugin;
use \Aventura\Edd\Bookings\Renderer\RuleRendererAbstract;
use \Exception;

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

    const DIARY_RULE_NS = 'Aventura\\Diary\\Bookable\\Availability\\Timetable\\Rule\\';
    const RENDERER_NS = 'Aventura\\Edd\\Bookings\\Renderer\\';
    
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
        \add_meta_box(
                'edd-bk-rules', 'Rules', array($this, 'renderRulesMetabox'), static::SLUG, 'normal', 'core'
        );
    }

    /**
     * Renders the rules metabox.
     */
    public function renderRulesMetabox($post)
    {
        $timetable = (empty($post->post_id))
                ? $this->getPlugin()->getTimetableController()->getFactory()->create(array('id' => 0))
                : $this->getPlugin()->getTimetableController()->get($post->post_id);
        echo $this->renderRulesTable($timetable);
    }

    /**
     * Renders the rules table UI for a particular timetable instance.
     * 
     * @param Timetable $timetable The timetable instance.
     * @return string The rendered output.
     */
    public function renderRulesTable(Timetable $timetable)
    {
        // $timetable->addRule(new DotwRangeRule(4, 7));
        // $timetable->addRule(new WeekNumRangeRule(10, 13));
        ob_start(); ?>
        <div class="edd-bk-timetable-container">
            <table class="widefat">
                <thead>
                    <?php
                    foreach ($this->getRulesTableColumns() as $columnId => $columnLabel) {
                        printf('<th id="%1$s">%2$s</th>', $columnId, $columnLabel);
                    }
                    ?>
                </thead>
                <tbody>
                    <?php
                    foreach ($timetable->getRules() as $rule) {
                        printf('<tr>%s</tr>', $this->renderRule($rule));
                    }
                    ?>
                </tbody>
            </table>
        </div>
        <?php
        return ob_get_clean();
    }

    /**
     * Render the table row for a specific rule instance.
     * 
     * @param RangeRuleAbstract $rule The rule.
     * @return string The rendered HTML.
     */
    public function renderRule(RangeRuleAbstract $rule)
    {
        $renderer = $this->getRuleRenderer($rule);
        $output = '';
        $output .= sprintf('<td>%s</td>', $this->renderMoveHandle());
        $output .= sprintf('<td>%s</td>', $renderer->getRuleName());
        $output .= sprintf('<td>%s</td>', $renderer->renderRangeStart());
        $output .= sprintf('<td>%s</td>', $renderer->renderRangeEnd());
        $output .= sprintf('<td>%s</td>', $renderer->renderAvailable());
        $output .= sprintf('<td>%s</td>', $this->renderRemoveHandle());
        return $output;
    }

    /**
     * Gets a renderer instance to use for a specific rule.
     * 
     * @param RangeRuleAbstract $rule The rule.
     * @return RuleRendererAbstract The renderer instance.
     * @throws Exception If the renderer for the given rule does not exist.
     */
    public function getRuleRenderer(RangeRuleAbstract $rule)
    {
        $ruleClass = get_class($rule);
        $renderers = $this->getRuleRenderers();
        if (!isset($renderers[$ruleClass])) {
            throw new Exception(sprintf('Renderer class for rule type "%s" is not specified!', $ruleClass));
        }
        $rendererClass = $renderers[$ruleClass];
        if (!class_exists($rendererClass)) {
            throw new Exception(sprintf('Renderer class "%s" does not exist!', $rendererClass));
        }
        return new $rendererClass($rule);
    }

    /**
     * Gets the rule renderers.
     * 
     * @return array An array with the rule classes as array keys and their respective renderer class names as array values.
     */
    public function getRuleRenderers()
    {
        $defaultRenderers = array(
                static::DIARY_RULE_NS . 'DotwRangeRule' => static::RENDERER_NS . 'DotwRangeRenderer',
                static::DIARY_RULE_NS . 'WeekNumRangeRule' => static::RENDERER_NS . 'WeekNumRangeRenderer'
        );
        $filteredRenderers = \apply_filters('edd_bk_timetable_rule_names', $defaultRenderers);
        return $filteredRenderers;
    }

    /**
     * Gets the table columns.
     * 
     * @return array An assoc array with column IDs as array keys and column labels as array values.
     */
    public function getRulesTableColumns()
    {
        $textDomain = $this->getPlugin()->getI18n()->getDomain();
        $columns = array(
                'move'      => '',
                'rule-type' => __('Rule Type', $textDomain),
                'start'     => __('Start', $textDomain),
                'end'       => __('End', $textDomain),
                'available' => __('Available', $textDomain),
                'remove'    => '',
        );
        $filteredColumns = \apply_filters('edd_bk_timetable_rules_table_columns', $columns);
        return $filteredColumns;
    }

    /**
     * Renders the row move handle.
     * 
     * @return string
     */
    public function renderMoveHandle()
    {
        return '<i class="fa fa-arrows-v edd-bk-move-handle"></i>';
    }

    /**
     * Renders the row remove handle.
     * 
     * @return string
     */
    public function renderRemoveHandle()
    {
        return '<i class="fa fa-times edd-bk-remove-handle"></i>';
    }

    /**
     * Registers the WordPress hooks.
     */
    public function hook()
    {
        $this->getPlugin()->getHookManager()
                ->addAction('init', $this, 'register')
                ->addAction('add_meta_boxes', $this, 'addMetaboxes');
    }

}
