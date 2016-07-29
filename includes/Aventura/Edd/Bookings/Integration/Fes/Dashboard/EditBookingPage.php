<?php

namespace Aventura\Edd\Bookings\Integration\Fes\Dashboard;

/**
 * Description of EditBookingPage
 *
 * @author Miguel Muscat <miguelmuscat93@gmail.com>
 */
class EditBookingPage extends DashboardPageAbstract
{
    
    public function render()
    {
        $bookingId = filter_input(INPUT_GET, 'booking_id', FILTER_SANITIZE_NUMBER_INT);
        $data = array(
            'booking_id' => $bookingId
        );
        echo $this->getPlugin()->renderView('Fes.Dashboard.Bookings.Edit', $data);
    }

}
