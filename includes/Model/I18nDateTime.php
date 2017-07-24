<?php

namespace Aventura\Edd\Bookings\Model;

use Aventura\Diary\DateTime as BaseDateTime;

/**
 * Extended DateTime class that supports internationalization.
 *
 * @since [*next-version*]
 */
class I18nDateTime extends BaseDateTime
{
    /**
     * {@inheritdoc}
     *
     * @since [*next-version*]
     */
    public function format($format)
    {
        return \date_i18n($format, $this->getTimestamp());
    }
}
