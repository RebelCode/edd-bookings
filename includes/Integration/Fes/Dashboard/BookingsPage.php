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
        $bookings = $this->getPlugin()->getIntegration('fes')->getBookingsForUser();
        $data = compact('bookings');
        echo $this->canUserView(get_current_user_id())
            ? $this->getPlugin()->renderView('Fes.Dashboard.Bookings.List', $data)
            : $this->getPlugin()->renderView('Fes.Dashboard.AccessDenied', array());
    }

    /**
     * Checks if a user with a specific ID can view this page.
     * 
     * @param integer $userId The ID of the user to check.
     * @return boolean True if the user can view the page, false if not.
     */
    public static function canUserView($userId)
    {
        return EDD_FES()->vendors->user_is_vendor($userId) || EDD_FES()->vendors->user_is_admin($userId);
    }

}
