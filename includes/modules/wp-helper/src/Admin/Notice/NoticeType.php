<?php

namespace RebelCode\Wp\Notice;

use MyCLabs\Enum\Enum;

/**
 * Notice types.
 *
 * @since [*next-version*]
 */
class NoticeType extends Enum
{
    const SUCCESS = 'success';
    const UPDATED = 'updated';
    const INFO    = 'info';
    const WARNING = 'warning';
    const ERROR   = 'error';
}
