<?php

namespace Aventura\Edd\Bookings\Timetable\Rule\Renderer;

use \Aventura\Diary\Bookable\Availability\Timetable\Rule\DateTimeRangeRule;
use \Aventura\Diary\DateTime;

/**
 * DateTimeRangeRenderer
 *
 * @author Miguel Muscat <miguelmuscat93@gmail.com>
 */
class DateTimeRangeRenderer extends RuleRendererAbstract
{

    /**
     * {@inheritdoc}
     */
    public function getRuleName()
    {
        return __('Custom', eddBookings()->getI18n()->getDomain());
    }
    
    /**
     * {@inheritdoc}
     */
    public function getRuleGroup()
    {
        return __('Common', eddBookings()->getI18n()->getDomain());
    }

    /**
     * Renders the field for the ranged rule's start value.
     * 
     * @param array $data Optional array of data. Default: array()
     * @return string The rendered output.
     */
    protected function _renderRangeStart(array $data = array())
    {
        return static::renderDateField($this->getRule()->getLower(), $data['class'], $data['name']);
    }

    /**
     * Renders the field for the ranged rule's start value.
     * 
     * @param array $data Optional array of data. Default: array()
     * @return string The rendered output.
     */
    protected function _renderRangeEnd(array $data = array())
    {
        return static::renderDateField($this->getRule()->getUpper(), $data['class'], $data['name']);
    }

    /**
     * Renders a date and time HTML field element.
     * 
     * @see DateTime
     * @param DateTime $date The date to show in the field.
     * @param string $class The class attribute value for the HTML element.
     * @param string $name The name attribute value for the HTML element.
     * @return string The rendered output.
     */
    public static function renderDateField(DateTime $date, $class, $name)
    {
        $dateValue = $date->getDate()->format('Y-m-d');
        $timeValue = $date->getTime()->format('H:i');
        $dateField = sprintf('<input type="date" value="%s" class="%s" name="%s" />', \esc_attr($dateValue), \esc_attr($class),
                \esc_attr($name));
        $timeField = sprintf('<input type="time" value="%s" class="$s" name="%s" />', \esc_attr($timeValue), \esc_attr($class),
                \esc_attr($name));
        return $dateField;
    }

    /**
     * {@inheritdoc}
     */
    public static function getDefault()
    {
        return new static(new DateTimeRangeRule(DateTime::now(), DateTime::now()));
    }


}
