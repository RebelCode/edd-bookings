<?php

namespace Aventura\Edd\Bookings\Renderer;

use \Aventura\Diary\DateTime\Duration;
use \Aventura\Edd\Bookings\Model\Booking;

/**
 * Description of BookingRenderer
 *
 * @author Miguel Muscat <miguelmuscat93@gmail.com>
 */
class BookingRenderer extends RendererAbstract
{

    /**
     * {@inheritdoc}
     */
    public function render(array $data = array())
    {
        /* @var $booking Booking */
        $booking = $this->getObject();
        $data['booking'] = $booking;
        return eddBookings()->renderView('Admin.Bookings.Info', $data);
    }

}
