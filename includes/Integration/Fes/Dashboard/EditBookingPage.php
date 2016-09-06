<?php

namespace Aventura\Edd\Bookings\Integration\Fes\Dashboard;

/**
 * Description of EditBookingPage
 *
 * @author Miguel Muscat <miguelmuscat93@gmail.com>
 */
class EditBookingPage extends DashboardPageAbstract
{
    
    /**
     * {@inheritdoc}
     */
    public function render()
    {
        $bookingId = filter_input(INPUT_GET, 'booking_id', FILTER_SANITIZE_NUMBER_INT);
        $data = array(
            'booking_id' => $bookingId
        );
        $booking = $this->getPlugin()->getBookingController()->get($bookingId);
        $serviceId = $booking->getServiceId();
        $service = get_post($serviceId);
        echo $this->canUserView(get_current_user_id(), $service)
            ? $this->getPlugin()->renderView('Fes.Dashboard.Bookings.Edit', $data)
            : $this->getPlugin()->renderView('Fes.Dashboard.AccessDenied', array());
    }

    /**
     * Checks if a user with a specific ID can view this page.
     * 
     * @param integer $userId The ID of the user to check.
     * @param Service $service The service in the context of the current page.
     * @return boolean True if the user can view the page, false if not.
     */
    public static function canUserView($userId, $service)
    {
        return ((int) $service->post_author) === ((int) $userId);
    }

}
