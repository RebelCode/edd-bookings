<?php

namespace RebelCode\EddBookings\Utils;

use \RebelCode\Diary\DateTime\DateTimeInterface;

/**
 * Something that can format dates and times into strings.
 *
 * @since [*next-version*]
 */
interface DateTimeFormatterInterface
{
    public function format($format, DateTimeInterface $dateTime);

    public function formatDate(DateTimeInterface $dateTime);

    public function formatTime(DateTimeInterface $dateTime);

    public function formatDatetime(DateTimeInterface $dateTime);

    public function formatDuration($duration);
}
