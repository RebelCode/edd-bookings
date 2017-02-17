<?php

namespace RebelCode\EddBookings\Utils;

use \RebelCode\Diary\DateTime\DateTime;
use \RebelCode\Diary\DateTime\DateTimeInterface;

/**
 * Description of DateTimeFormatter
 *
 * @since [*next-version*]
 */
class DateTimeFormatter implements DateTimeFormatterInterface
{

    protected $dateFormat;
    protected $timeFormat;
    protected $formatPattern;

    public function __construct($dateFormat, $timeFormat, $formatPattern)
    {
        $this->setDateFormat($dateFormat)
            ->setTimeFormat($timeFormat)
            ->setFormatPattern($formatPattern);
    }

    public function getDateFormat()
    {
        return $this->dateFormat;
    }

    public function getTimeFormat()
    {
        return $this->timeFormat;
    }

    public function getFormatPattern()
    {
        return $this->formatPattern;
    }

    public function setDateFormat($dateFormat)
    {
        $this->dateFormat = $dateFormat;

        return $this;
    }

    public function setTimeFormat($timeFormat)
    {
        $this->timeFormat = $timeFormat;

        return $this;
    }

    public function setFormatPattern($formatPattern)
    {
        $this->formatPattern = $formatPattern;

        return $this;
    }

    public function getDateTimeFormat()
    {
        return sprintf($this->getFormatPattern(), $this->getDateFormat(), $this->getTimeFormat());
    }

    public function formatDate(DateTimeInterface $dateTime)
    {
        return \date_i18n($this->getDateFormat(), $dateTime->getTimestamp());
    }

    public function formatTime(DateTimeInterface $dateTime)
    {
        return \date_i18n($this->getTimeFormat(), $dateTime->getTimestamp());
    }

    public function formatDatetime(DateTimeInterface $dateTime)
    {
        return \date_i18n($this->getDateTimeFormat(), $dateTime->getTimestamp());
    }

    public function format($format, DateTimeInterface $dateTime)
    {
        return date($format, $dateTime->getTimestamp());
    }

    public function formatDuration($duration)
    {
        $zero     = DateTime::createFromTimestampUTC(0);
        $dateTime = DateTime::createFromTimestampUTC($duration);

        return $dateTime->diffForHumans($zero, true);
    }
}
