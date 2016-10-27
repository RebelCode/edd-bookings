<?php

namespace Aventura\Edd\Bookings\Availability\Rule\Renderer;

use \Aventura\Diary\DateTime\Day;

/**
 * Description of WeekdaysTimeRangeRenderer
 *
 * @author Miguel Muscat <miguelmuscat93@gmail.com>
 */
class WeekdaysTimeRangeRenderer extends DotwGroupTimeRangeRendererAbstract
{
    
    const CLASSNAME = 'WeekdaysTimeRule';
    
    /**
     * {@inheritdoc}
     */
    public function getRuleName()
    {
        return __('Weekdays', 'eddbk');
    }

}
