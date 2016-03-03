<?php

namespace Aventura\Edd\Bookings\Renderer;

/**
 * Description of WeekNumRangeRenderer
 *
 * @author Miguel Muscat <miguelmuscat93@gmail.com>
 */
class WeekNumRangeRenderer extends RuleRendererAbstract
{

    /**
     * {@inheritdoc}
     */
    public function getRuleName()
    {
        return __('Weeks', eddBookings()->getI18n()->getDomain());
    }

    /**
     * {@inheritdoc}
     */
    protected function _renderRangeStart(array $data = array())
    {
        return $this->_renderWeekNumField($this->getRule()->getLower(), $data['class'], $data['name']);
    }
    
    /**
     * {@inheritdoc}
     */
    protected function _renderRangeEnd(array $data = array())
    {
        return $this->_renderWeekNumField($this->getRule()->getUpper(), $data['class'], $data['name']);
    }
    
    /**
     * Renders the week number field.
     * 
     * @param integer $weekNum The week number.
     * @param string $class The class attribute of the HTML element.
     * @param string $name The name attribute of the HTML element.
     * @return string The rendered output.
     */
    protected function _renderWeekNumField($weekNum, $class, $name)
    {
        return sprintf('<input type="number" min="1" max="52" step="1" class="%s" name="%s" value="%s" />',
                $class, $name, $weekNum);
    }

}
