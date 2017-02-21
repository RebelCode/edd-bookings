<?php

namespace RebelCode\EddBookings\Admin\Calendar\Block;

use \RebelCode\EddBookings\Block\Html\CompositeTag;
use \RebelCode\EddBookings\Block\Html\RegularTag;

/**
 * Description of AdminCalendarPopupBlock
 *
 * @since [*next-version*]
 */
class CalendarPopupBlock extends CompositeTag
{
    const TAG_NAME = 'div';

    public function __construct($showHeader = true, array $attributes = array())
    {
        parent::__construct(static::TAG_NAME, $attributes, array());

        $class = $this->getAttribute('class', '');
        $this->setAttribute('class', $class . ' edd-bk-modal edd-bk-bookings-calendar-info');

        if ($showHeader) {
            $this->addChild(new RegularTag('h4', array(), __('Booking Info', 'eddbk')));
        }

        $this->addChild(new RegularTag('div'));
    }
}
