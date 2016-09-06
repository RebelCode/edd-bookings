<?php

namespace Aventura\Edd\Bookings\Availability\Rule\Renderer;

use \Aventura\Diary\DateTime\Day;

/**
 * Description of AllWeekTimeRangeRenderer
 *
 * @author Miguel Muscat <miguelmuscat93@gmail.com>
 */
class AllWeekTimeRangeRenderer extends DotwGroupTimeRangeRendererAbstract
{
    
    const CLASSNAME = 'AllWeekTimeRule';
    
    /**
     * {@inheritdoc}
     */
    public function getRuleName()
    {
        return __('All Week', eddBookings()->getI18n()->getDomain());
    }

}
