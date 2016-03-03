<?php

namespace Aventura\Edd\Bookings\Renderer;

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
        return __('Days', eddBookings()->getI18n()->getDomain());
    }

    /**
     * Renders a day of the week select HTML element.
     * 
     * @see \Aventura\Diary\DateTime\Day
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
            $options .= sprintf('<option value="%s" %s>%s</option>', $ordinal, $selected, ucfirst(strtolower($dayName)));
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

}
