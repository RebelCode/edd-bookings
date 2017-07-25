<?php

namespace Aventura\Edd\Bookings\Availability\Rule\Renderer;

use \Aventura\Diary\Bookable\Availability\Timetable\Rule\MonthRangeRule;
use \Aventura\Diary\DateTime\Month;

/**
 * Description of MonthRangeRenderer
 *
 * @author Miguel Muscat <miguelmuscat93@gmail.com>
 */
class MonthRangeRenderer extends RuleRendererAbstract
{

    /**
     * {@inheritdoc}
     */
    public function getRuleName()
    {
        return __('Months', 'eddbk');
    }
    
    /**
     * {@inheritdoc}
     */
    public function getRuleGroup()
    {
        return __('Common', 'eddbk');
    }

    /**
     * Renders the field for the ranged rule's start value.
     * 
     * @param array $data Optional array of data. Default: array()
     * @return string The rendered output.
     */
    protected function _renderRangeStart(array $data = array())
    {
        return static::renderMonthSelector($this->getRule()->getLower(), $data['class'], $data['name']);
    }

    /**
     * Renders the field for the ranged rule's end value.
     * 
     * @param array $data Optional array of data. Default: array()
     * @return string The rendered output.
     */
    protected function _renderRangeEnd(array $data = array())
    {
        return static::renderMonthSelector($this->getRule()->getUpper(), $data['class'], $data['name']);
    }

    /**
     * Renders a month select HTML element.
     * 
     * @see Month
     * @param integer $selectedMonth The selected month, as an ordinal.
     * @param string $class The class attribute value for the HTML element.
     * @param string $name The name attribute value for the HTML element.
     * @return string The rendered output.
     */
    public static function renderMonthSelector($selectedMonth, $class, $name)
    {
        $motnhs = Month::getAll();
        $options = '';
        foreach ($motnhs as $monthName => $ordinal) {
            $selected = selected($selectedMonth, $ordinal, false);
            $fMonthName =  ucfirst(strtolower($monthName));
            $options .= sprintf('<option value="%s" %s>%s</option>',
                $ordinal,
                $selected,
                translate($fMonthName)
            );
        }
        $output = sprintf('<select class="%s" name="%s">%s</select>', $class, $name, $options);
        return $output;
    }

    /**
     * {@inheritdoc}
     */
    public static function getDefault()
    {
        return new static(new MonthRangeRule(1, 1));
    }

}
