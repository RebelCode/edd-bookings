<?php

namespace Aventura\Edd\Bookings\Availability\Rule\Renderer;

use \Aventura\Diary\Bookable\Availability\Timetable\Rule\DotwRangeRule;
use \Aventura\Diary\DateTime\Day;

/**
 * Renders a DotwRangeRule instance.
 *
 * @author Miguel Muscat <miguelmuscat93@gmail.com>
 */
class DotwRangeRenderer extends RuleRendererAbstract
{

    /**
     * {@inheritdoc}
     */
    public function getRuleName()
    {
        return __('Days', 'eddbk');
    }
    
    /**
     * {@inheritdoc}
     */
    public function getRuleGroup()
    {
        return __('Common', 'eddbk');
    }

    /**
     * Renders a day of the week select HTML element.
     * 
     * @see Day
     * @param integer $selectedDay The selected day index.
     * @param string $class The class element attribute value.
     * @param string $name The element name attribute value.
     * @return string HTML string containing the output.
     */
    protected function _renderDotwSelect($selectedDay, $class, $name)
    {
        $days = Day::getAll();
        $options = '';
        foreach ($days as $dayName => $ordinal) {
            $selected = selected($selectedDay, $ordinal, false);
            $fDayName = ucfirst(strtolower($dayName));
            $options .= sprintf('<option value="%s" %s>%s</option>',
                $ordinal,
                $selected,
                translate($fDayName)
            );
        }
        $output = sprintf('<select class="%s" name="%s">%s</select>', $class, $name, $options);
        return $output;
    }

    /**
     * {@inheritdoc}
     */
    protected function _renderRangeStart(array $data = array())
    {
        return $this->_renderDotwSelect($this->getRule()->getLower(), $data['class'], $data['name']);
    }

    /**
     * {@inheritdoc}
     */
    protected function _renderRangeEnd(array $data = array())
    {
        return $this->_renderDotwSelect($this->getRule()->getUpper(), $data['class'], $data['name']);
    }

    /**
     * {@inheritdoc}
     */
    public static function getDefault()
    {
        return new static(new DotwRangeRule(Day::MONDAY, Day::MONDAY));
    }

}
