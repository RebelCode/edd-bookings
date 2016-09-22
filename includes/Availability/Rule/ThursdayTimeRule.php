<?php

namespace Aventura\Edd\Bookings\Availability\Rule;

use \Aventura\Diary\DateTime\Day;

/**
 * Description of ThursdayTimeRule
 *
 * @author Miguel Muscat <miguelmuscat93@gmail.com>
 */
class ThursdayTimeRule extends SingleDotwTimeRule
{

    const DOTW = Day::THURSDAY;

}
