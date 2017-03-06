<?php

namespace RebelCode\Wp\Notice;

/**
 * Notice dismissal modes.
 *
 * @since [*next-version*]
 */
class NoticeDismissMode extends Enum
{
    const NONE   = 'none';
    const NORMAL = 'frontend';
    const AJAX   = 'ajax';
}
