<?php

namespace Aventura\Edd\Bookings\Integration\Fes\Dashboard;

/**
 * The Bookings page for the frontend FES dashboard.
 *
 * @author Miguel Muscat <miguelmuscat93@gmail.com>
 */
class BookingsPage extends DashboardPageAbstract
{

    /**
     * {@inheritdoc}
     */
    public function render()
    {
        if (!EDD_FES()->vendors->vendor_can_view_orders()) {
            EDD_FES()->templates->fes_get_template_part('frontend', 'dashboard');
        } else {
            $bookings = $this->getPlugin()->getIntegration('fes')->getBookingsForUser();
            $data = compact('bookings');
            echo $this->getPlugin()->renderView('Fes.Dashboard.Bookings.List', $data);
        }
    }

}
