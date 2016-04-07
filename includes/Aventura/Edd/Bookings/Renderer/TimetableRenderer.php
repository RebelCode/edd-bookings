<?php

namespace Aventura\Edd\Bookings\Renderer;

use \Aventura\Diary\Bookable\Availability\Timetable\Rule\RangeRuleAbstract;
use \Aventura\Edd\Bookings\Model\Timetable;
use \Aventura\Edd\Bookings\Renderer\RendererAbstract;
use \Aventura\Edd\Bookings\Timetable\Rule\Renderer\RuleRendererInterface;
use \Exception;
use \InvalidArgumentException;

/**
 * An object that can render a timetable.
 *
 * @author Miguel Muscat <miguelmuscat93@gmail.com>
 */
class TimetableRenderer extends RendererAbstract
{

    // Namespace shortcut constants
    const DIARY_RULE_NS = 'Aventura\\Diary\\Bookable\\Availability\\Timetable\\Rule\\';
    const EDD_BK_RULE_NS = 'Aventura\\Edd\\Bookings\\Timetable\\Rule\\';
    const RENDERER_NS = 'Aventura\\Edd\\Bookings\\Timetable\\Rule\\Renderer\\';

    /**
     * Constructs a new instance.
     * 
     * @param Timetable $timetable The timetable to render.
     */
    public function __construct(Timetable $timetable)
    {
        parent::__construct($timetable);
    }

    /**
     * {@inheritdoc}
     * 
     * @return Timetable
     */
    public function getObject()
    {
        return parent::getObject();
    }

    /**
     * {@inheritdoc}
     */
    public function render(array $data = array())
    {
        $timetable = $this->getObject();
        $textDomain = eddBookings()->getI18n()->getDomain();
        ob_start();
        // Use nonce for verification
        \wp_nonce_field('edd_bk_save_meta', 'edd_bk_timetable');
        // Use nonce for ajax
        \wp_nonce_field('edd_bk_timetable_ajax', 'edd_bk_timetable_ajax_nonce');
        ?>
        <div class="edd-bk-timetable-container" data-id="<?php echo $timetable->getId(); ?>">
            <table class="widefat">
                <thead>
                    <?php
                    foreach (static::getRulesTableColumns() as $columnId => $columnLabel) {
                        printf('<th class="edd-bk-col-%s">%s</th>', $columnId, $columnLabel);
                    }
                    ?>
                </thead>
                <tbody>
                    <tr class="edd-bk-if-no-rules">
                        <td></td>
                        <td colspan="4">
                            <p><?php _e('There are no rules yet! Click the "Add Rule" button to get started. ', $textDomain); ?></p>
                        </td>
                        <td></td>
                    </tr>
                    <?php
                    foreach ($timetable->getRules() as $rule) {
                        echo static::renderRule($rule);
                    }
                    ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="4">
                            <span class=""edd-bk-timetable-help>
                                <?php
                                printf(__('Need help? Check out our <a %s>documentation</a>.', $textDomain),
                                        'href="http://docs.easydigitaldownloads.com/category/1100-bookings" target="_blank"');
                                ?>
                            </span>
                        </td>
                        <td colspan="2" class="edd-bk-timetable-add-rule">
                            <button class="button button-secondary" type="button">
                                <i class="edd-bk-add-rule-icon fa fa-plus fa-fw"></i>
                                <i class="edd-bk-add-rule-loading fa fa-hourglass-half fa-fw"></i>
                                <?php _e('Add Rule', $textDomain); ?>
                            </button>
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
        <?php
        return ob_get_clean();
    }

    /**
     * Renders the timetable calendar preview
     */
    public function renderPreview()
    {
        $timetable = $this->getObject();
        $id = $timetable->getId();
        $textDomain = eddBookings()->getI18n()->getDomain();
        ob_start();
        ?>
        <div class="edd-bk-calendar-preview">
            <label>
                <?php _e('Preview using:', $textDomain); ?>
                <select class="edd-bk-calendar-preview-service">
                    <?php
                    $availabilities = eddBookings()->getAvailabilityController()->getAvailabilitiesForTimetable($id);
                    $availabilityIds = array_map(function($item) {
                        return $item->getId();
                    }, $availabilities);
                    $services = eddBookings()->getServiceController()->getServicesForAvailability($availabilityIds);
                    foreach ($services as $service) {
                        $serviceId = $service->getId();
                        $serviceName = \get_the_title($serviceId);
                        printf('<option value="%s">%s</option>', $serviceId, $serviceName);
                    }
                    ?>
                </select>
            </label>
            <hr/>
            <div class="edd-bk-datepicker-container">
                <div class="edd-bk-datepicker-skin">
                    <div class="edd-bk-datepicker"></div>
                </div>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }

    /**
     * Gets the table columns.
     * 
     * @return array An assoc array with column IDs as array keys and column labels as array values.
     */
    public static function getRulesTableColumns()
    {
        $textDomain = eddBookings()->getI18n()->getDomain();
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
     * Renders the selector HTML element for the rule types.
     * 
     * @param type $selectedRule
     * @return type
     */
    public static function renderRangeTypeSelector($selectedRule = null)
    {
        $optionGroups = '';
        foreach (static::getRuleTypesGrouped() as $group => $rules) {
            $optionGroups .= sprintf('<optgroup label="%s">', $group);
            $options = '';
            foreach ($rules as $ruleClass => $ruleRendererClass) {
                $ruleName = $ruleRendererClass::getDefault()->getRuleName();
                $selected = \selected($ruleClass, $selectedRule, false);
                $options .= sprintf('<option value="%s" %s>%s</option>', $ruleClass, $selected, $ruleName);
            }
            $optionGroups .= sprintf('%s</optgroup>', $options);
        }
        return sprintf('<select>%s</select>', $optionGroups);
    }

    /**
     * Render the table row for a specific rule instance.
     * 
     * @param RangeRuleAbstract|string|null $rule The rule.
     * @return string The rendered HTML.
     */
    public static function renderRule($rule)
    {
        if (is_null($rule)) {
            $ruleClass = key(static::getRuleTypes());
            $rendererClass = current(static::getRuleTypes());
            $renderer = $rendererClass::getDefault();
        } elseif (is_string($rule)) {
            $ruleClass = $rule;
            $rendererClass = static::getRuleRendererClassName($ruleClass);
            $renderer = $rendererClass::getDefault();
        } elseif (is_a($rule, '\\Aventura\\Diary\\Bookable\\Availability\\Timetable\\Rule\\RangeRuleAbstract')) {
            // Get the rule renderer
            $ruleClass = get_class($rule);
            $rendererClass = static::getRuleRendererClassName($ruleClass);
            $renderer = new $rendererClass($rule);
        } else {
            throw new InvalidArgumentException('Argument is not a string, RangeRuleAbstract instance or null.');
        }
        // Generate the rule type selector output
        $ruleSelector = static::renderRangeTypeSelector($ruleClass);
        // Generate output
        $output = '';
        $tdLayout = '<td class="%s">%s</td>';
        $output .= sprintf($tdLayout, 'edd-bk-rule-move-handle', static::renderMoveHandle());
        $output .= sprintf($tdLayout, 'edd-bk-rule-selector', $ruleSelector);
        $output .= sprintf($tdLayout, 'edd-bk-rule-start', $renderer->renderRangeStart());
        $output .= sprintf($tdLayout, 'edd-bk-rule-end', $renderer->renderRangeEnd());
        $output .= sprintf($tdLayout, 'edd-bk-rule-available', $renderer->renderAvailable());
        $output .= sprintf($tdLayout, 'edd-bk-rule-remove-handle', static::renderRemoveHandle());
        return sprintf('<tr>%s</tr>', $output);
    }

    /**
     * Renders the row move handle.
     * 
     * @return string
     */
    public static function renderMoveHandle()
    {
        return '<i class="fa fa-arrows-v edd-bk-move-handle"></i>';
    }

    /**
     * Renders the row remove handle.
     * 
     * @return string
     */
    public static function renderRemoveHandle()
    {
        return '<i class="fa fa-times edd-bk-remove-handle"></i>';
    }

    /**
     * Gets the rule types.
     * 
     * @return array An associative array with rule ID as array keys and their respective renderer class names as
     *               array keys.
     */
    public static function getRuleTypes()
    {
        $ruleTypes = array(
                static::EDD_BK_RULE_NS . 'DotwRule'          => static::RENDERER_NS . 'DotwRangeRenderer',
                static::EDD_BK_RULE_NS . 'WeekNumRule'       => static::RENDERER_NS . 'WeekNumRangeRenderer',
                static::EDD_BK_RULE_NS . 'MonthRule'         => static::RENDERER_NS . 'MonthRangeRenderer',
                static::EDD_BK_RULE_NS . 'CustomDateRule'    => static::RENDERER_NS . 'DateTimeRangeRenderer',
                static::EDD_BK_RULE_NS . 'MondayTimeRule'    => static::RENDERER_NS . 'MondayTimeRangeRenderer',
                static::EDD_BK_RULE_NS . 'TuesdayTimeRule'   => static::RENDERER_NS . 'TuesdayTimeRangeRenderer',
                static::EDD_BK_RULE_NS . 'WednesdayTimeRule' => static::RENDERER_NS . 'WednesdayTimeRangeRenderer',
                static::EDD_BK_RULE_NS . 'ThursdayTimeRule'  => static::RENDERER_NS . 'ThursdayTimeRangeRenderer',
                static::EDD_BK_RULE_NS . 'FridayTimeRule'    => static::RENDERER_NS . 'FridayTimeRangeRenderer',
                static::EDD_BK_RULE_NS . 'SaturdayTimeRule'  => static::RENDERER_NS . 'SaturdayTimeRangeRenderer',
                static::EDD_BK_RULE_NS . 'SundayTimeRule'    => static::RENDERER_NS . 'SundayTimeRangeRenderer',
                static::EDD_BK_RULE_NS . 'AllWeekTimeRule'   => static::RENDERER_NS . 'AllWeekTimeRangeRenderer',
                static::EDD_BK_RULE_NS . 'WeekdaysTimeRule'  => static::RENDERER_NS . 'WeekdaysTimeRangeRenderer',
                static::EDD_BK_RULE_NS . 'WeekendTimeRule'   => static::RENDERER_NS . 'WeekendTimeRangeRenderer',
        );
        $filtered = \apply_filters('edd_bk_timetable_rule_types', $ruleTypes);
        return $filtered;
    }

    /**
     * Gets the rules, grouped according to their renderer's group.
     * 
     * @return array An associative array with group names as array keys and associative subarrays as array values.
     *               Each subarray has rule class name as array keys and their respective renderer class name as
     *               array values.
     */
    public static function getRuleTypesGrouped()
    {
        $ruleTypes = static::getRuleTypes();
        $grouped = array();
        foreach ($ruleTypes as $ruleClass => $rendererClass) {
            /* @var $defaultRenderer RuleRendererInterface */
            $defaultRenderer = $rendererClass::getDefault();
            $group = $defaultRenderer->getRuleGroup();
            // Create group if not in $grouped array
            if (!isset($grouped[$group])) {
                $grouped[$group] = array();
            }
            // Add to the $grouped array
            $grouped[$group][$ruleClass] = $rendererClass;
        }
        $filtered = \apply_filters('edd_bk_timetable_rule_types_grouped', $grouped);
        return $filtered;
    }

    /**
     * Gets the renderer class name for a specific rule.
     * 
     * @param string $ruleClass The rule class.
     * @return string The renderer class name.
     * @throws Exception If the rule class given does not exist.
     */
    public static function getRuleRendererClassName($ruleClass)
    {
        $ruleTypes = static::getRuleTypes();
        $sanitizedRuleClass = str_replace('\\\\', '\\', $ruleClass);
        if (!isset($ruleTypes[$sanitizedRuleClass])) {
            throw new Exception(sprintf('The rule type class "%s" does not exist!', $sanitizedRuleClass));
        }
        $rendererClass = $ruleTypes[$sanitizedRuleClass];
        return $rendererClass;
    }

}
