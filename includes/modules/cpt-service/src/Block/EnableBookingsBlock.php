<?php

namespace RebelCode\EddBookings\CustomPostType\Service\Block;

use \RebelCode\EddBookings\Block\Html\CheckboxBlock;

/**
 * Description of EnableBookingsBlock
 *
 * @since [*next-version*]
 */
class EnableBookingsBlock extends AbstractServiceOptionBlock
{
    /**
     * {@inheritdoc}
     *
     * @since [*next-version*]
     */
    protected function _getOutput()
    {
        return new CheckboxBlock(
            'edd-bk-bookings-enabled',
            'service[bookings_enabled]',
            '1',
            $this->getService()->getBookingsEnabled()
        );
    }

}
