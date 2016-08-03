<?php

namespace Aventura\Edd\Bookings\Integration\Fes\Dashboard;

/**
 * 
 *
 * @author Miguel Muscat <miguelmuscat93@gmail.com>
 */
class BookingsCalendarPage extends DashboardPageAbstract
{
    
    /**
     * {@inheritdoc}
     */
    public function render()
    {
        echo EDD_FES()->vendors->vendor_can_view_orders()
                ? $this->getPlugin()->renderView('Fes.Dashboard.Bookings.Calendar', array())
                : $this->getPlugin()->renderView('Fes.Dashboard.AccessDenied', array());
    }

}
