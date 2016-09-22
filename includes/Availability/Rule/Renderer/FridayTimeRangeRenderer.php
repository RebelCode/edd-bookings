<?php

namespace Aventura\Edd\Bookings\Availability\Rule\Renderer;

use \Aventura\Diary\DateTime\Day;

/**
 * Description of FridayTimeRenderer
 *
 * @author Miguel Muscat <miguelmuscat93@gmail.com>
 */
class FridayTimeRangeRenderer extends DotwTimeRangeRendererAbstract
{

    const DOTW = 5;
    const CLASSNAME = 'FridayTimeRule';

}
