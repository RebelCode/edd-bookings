<?php

namespace Aventura\Edd\Bookings\Availability\Rule\Renderer;

use \Aventura\Diary\DateTime;
use \Aventura\Diary\DateTime\Day;

/**
 * Description of DotwTimeRangeRenderer
 *
 * @author Miguel Muscat <miguelmuscat93@gmail.com>
 */
abstract class DotwTimeRangeRendererAbstract extends RuleRendererAbstract
{

    const DOTW = 0;
    const NS = '\\Aventura\\Edd\\Bookings\\Availability\\Rule\\';
    const CLASSNAME = '';
    
    /**
     * {@inheritdoc}
     */
    public function getRuleName()
    {
        $days = array_flip(Day::getAll());
        $dotw = $days[static::DOTW];
        return ucfirst(strtolower($dotw));
    }

    /**
     * {@inheritdoc}
     */
    public function getRuleGroup()
    {
        return __('Time', 'eddbk');
    }
    
    /**
     * {@inheritdoc}
     */
    protected function _renderRangeStart(array $data = array())
    {
        return static::renderTimeField($this->getRule()->getLower(), $data['class'], $data['name']);
    }

    /**
     * {@inheritdoc}
     */
    protected function _renderRangeEnd(array $data = array())
    {
        return static::renderTimeField($this->getRule()->getUpper(), $data['class'], $data['name']);
    }

    /**
     * Renders a time field.
     * 
     * @param DateTime $time The time value to use in the field.
     * @param string $class The class attribute value of the HTML element.
     * @param string $name The name attribute value of the HTML element.
     * @return string The rendered HTML.
     */
    public static function renderTimeField(DateTime $time, $class, $name)
    {
        $value = eddBookings()->utcTimeToServerTime($time->getTime())->format('H:i:s');
        return sprintf('<input type="time" value="%s" class="%s" name="%s" />', \esc_attr($value), \esc_attr($class),
                \esc_attr($name));
    }
    
    /**
     * {@inheritdoc}
     */
    public static function getDefault()
    {
        $classname = static::NS . static::CLASSNAME;
        $instance = new $classname(eddBookings()->serverTimeToUtcTime(new Datetime(0)),
                eddBookings()->serverTimeToUtcTime(new Datetime(86399)));
        return new static($instance);
    }

}
