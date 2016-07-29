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
        echo $this->getPlugin()->renderView('Fes.Dashboard.Bookings.Calendar', array());
    }

}
