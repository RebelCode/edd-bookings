<?php

namespace RebelCode\EddBookings\Admin\Calendar\Block;

use \RebelCode\EddBookings\Block\AbstractBlock;
use \RebelCode\EddBookings\Block\Html\CompositeTag;
use \RebelCode\EddBookings\Block\Html\FaIcon;

/**
 * Page block for the admin calendar page.
 *
 * @since [*next-version*]
 */
class CalendarPage extends AbstractBlock
{
    protected function _getOutput()
    {
        return new CompositeTag('div', array('class' => 'wrap'), array(
            new CompositeTag('h1', array(), array(
                new FaIcon('calendar'), sprintf(' %s', __('Calendar', 'eddbk'))
            )),
            new CalendarBlock(true),
            \wp_nonce_field('edd_bk_calendar_ajax', 'edd_bk_calendar_ajax_nonce', true, false)
        ));
    }
}
