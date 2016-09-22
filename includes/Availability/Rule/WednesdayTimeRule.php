<?php

namespace Aventura\Edd\Bookings\Availability\Rule;

use \Aventura\Diary\DateTime\Day;

/**
 * Description of WednesdayTimeRule
 *
 * @author Miguel Muscat <miguelmuscat93@gmail.com>
 */
class WednesdayTimeRule extends SingleDotwTimeRule
{

    const DOTW = Day::WEDNESDAY;

}
