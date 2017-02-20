<?php

namespace RebelCode\EddBookings\Model;

use \MyCLabs\Enum\Enum;

/**
 * Description of SessionUnit
 *
 * @since [*next-version*]
 */
class SessionUnit extends Enum
{
    const SECONDS = 1;
    const MINUTES = 60;
    const HOURS   = 3600;
    const DAYS    = 86400;
    const WEEKS   = 604800;
}
