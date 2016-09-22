<?php

namespace Aventura\Edd\Bookings\Availability\Rule\Renderer;

use \Aventura\Diary\DateTime;

/**
 * Description of DotwGroupTimeRangeRendererAbstract
 *
 * @author Miguel Muscat <miguelmuscat93@gmail.com>
 */
abstract class DotwGroupTimeRangeRendererAbstract extends DotwTimeRangeRendererAbstract
{
    
    /**
     * {@inheritdoc}
     */
    public function getRuleGroup()
    {
        return __('Time Groups', eddBookings()->getI18n()->getDomain());
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
