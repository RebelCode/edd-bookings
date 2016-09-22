<?php

namespace Aventura\Edd\Bookings\Availability\Rule\Renderer;

use \Aventura\Diary\DateTime\Day;

/**
 * Description of SundayTimeRule
 *
 * @author Miguel Muscat <miguelmuscat93@gmail.com>
 */
class SundayTimeRangeRenderer extends DotwTimeRangeRendererAbstract
{
    
    const DOTW = 7;
    const CLASSNAME = 'SundayTimeRule';
    
}
