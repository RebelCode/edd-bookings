<?php

namespace RebelCode\EddBookings\Admin\Calendar\Block;

use \RebelCode\EddBookings\Block\Html\CompositeTag;

/**
 * Description of AdminCalendarBlock
 *
 * @since [*next-version*]
 */
class CalendarBlock extends CompositeTag
{

    const TAG_NAME = 'div';

    public function __construct($showPopup = true, array $attributes = array())
    {
        parent::__construct(static::TAG_NAME, $attributes, array());

        $class = $this->getAttribute('class', '');
        $this->setAttribute('class', sprintf('edd-bk-bookings-calendar edd-bk-fc %s', $class));

        if ($showPopup) {
            $this->addChild(new CalendarPopupBlock());
        }
    }
}
