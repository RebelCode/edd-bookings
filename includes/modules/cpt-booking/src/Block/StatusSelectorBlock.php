<?php

namespace RebelCode\EddBookings\CustomPostType\Booking\Block;

use \RebelCode\EddBookings\Block\Html\SelectTag;
use \RebelCode\EddBookings\Model\Booking;

/**
 * Description of StatusSelectorBlock
 *
 * @since [*next-version*]
 */
class StatusSelectorBlock extends SelectTag
{

    public function __construct(Booking $booking, array $attributes = array())
    {
        parent::__construct($this->_getStatuses(), $booking->getStatus(), $attributes);
    }

    public function _getStatuses()
    {
        return array(
            'publish' => __('Confirmed', 'eddbk'),
            'draft'   => __('Draft')
        );

        // return BookingStatus::toArray();
    }
}
