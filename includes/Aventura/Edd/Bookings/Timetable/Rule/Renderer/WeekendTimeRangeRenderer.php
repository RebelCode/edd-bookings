<?php

namespace Aventura\Edd\Bookings\Timetable\Rule\Renderer;

use \Aventura\Diary\DateTime\Day;

/**
 * Description of WeekendTimeRangeRenderer
 *
 * @author Miguel Muscat <miguelmuscat93@gmail.com>
 */
class WeekendTimeRangeRenderer extends DotwGroupTimeRangeRendererAbstract
{
    
    const CLASSNAME = 'WeekendTimeRule';
    
    /**
     * {@inheritdoc}
     */
    public function getRuleName()
    {
        return __('Weekend', eddBookings()->getI18n()->getDomain());
    }

}
