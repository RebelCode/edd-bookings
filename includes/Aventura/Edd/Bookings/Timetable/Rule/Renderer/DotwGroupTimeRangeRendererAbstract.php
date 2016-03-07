<?php

namespace Aventura\Edd\Bookings\Timetable\Rule\Renderer;

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
        return new static(new $classname(new DateTime(0), new DateTime(86399)));
    }

}
