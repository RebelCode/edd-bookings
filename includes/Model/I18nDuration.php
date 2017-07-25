<?php

namespace Aventura\Edd\Bookings\Model;

use Aventura\Diary\DateTime\Duration as BaseDuration;

/**
 * Extended Duration class that supports internationalization.
 *
 * @since [*next-version*]
 */
class I18nDuration extends BaseDuration
{
    /**
     * {@inheritdoc}
     *
     * @since [*next-version*]
     */
    public static function beautify($seconds, $separator = ', ')
    {
        $result = array();

        foreach (array_reverse(self::$unitsInSeconds) as $unit => $inSeconds) {
            $amount = floor($seconds / $inSeconds);

            if ($amount > 0) {
                $suffix   = $amount > 1 ? 's' : '';
                $unitText = $unit . $suffix;
                $result[] = sprintf('%1$s %2$s', $amount, translate($unitText, 'eddbk'));
            }

            $seconds = floor($seconds % $inSeconds);
        }

        return implode($separator, $result);
    }

    /**
     * {@inheritdoc}
     *
     * @since [*next-version*]
     */
    public function __toString()
    {
        return static::beautify($this->getSeconds());
    }
}
