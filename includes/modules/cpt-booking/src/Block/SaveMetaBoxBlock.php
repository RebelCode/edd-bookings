<?php

namespace RebelCode\EddBookings\CustomPostType\Booking\Block;

use \RebelCode\EddBookings\Block\AbstractBlock;
use \RebelCode\EddBookings\Block\Html\InputTag;
use \RebelCode\EddBookings\Block\Html\RegularTag;
use \RebelCode\EddBookings\Controller\BookingController;
use \RebelCode\EddBookings\Model\Booking;
use \RebelCode\WordPress\Admin\Tooltip;

/**
 * Description of SaveMetaBoxBlock
 *
 * @since [*next-version*]
 */
class SaveMetaBoxBlock extends AbstractBlock
{
    /**
     * The booking controller instance.
     *
     * @since [*next-version*]
     *
     * @var BookingController
     */
    protected $bookingController;

    /**
     * Constructor.
     *
     * @since [*next-version*]
     *
     * @param BookingController $bookingController The booking controller instance.
     */
    public function __construct(BookingController $bookingController)
    {
        $this->setBookingController($bookingController);
    }

    /**
     * Gets the booking controller.
     *
     * @since [*next-version*]
     *
     * @return BookingController The booking controller instance.
     */
    public function getBookingController()
    {
        return $this->bookingController;
    }

    /**
     * Sets the booking controller.
     *
     * @since [*next-version*]
     *
     * @param BookingController $bookingController The new booking controller instance.
     *
     * @return $this This instance.
     */
    public function setBookingController(BookingController $bookingController)
    {
        $this->bookingController = $bookingController;

        return $this;
    }

    /**
     * Gets the booking.
     *
     * @since [*next-version*]
     *
     * @return Booking The booking instance.
     */
    protected function _getBooking()
    {
        global $post;

        return $this->getBookingController()->get($post->ID);
    }

    /**
     * {@inheritdoc}
     *
     * @since [*next-version*]
     */
    protected function _getOutput()
    {
        $booking        = $this->_getBooking();
        $statusDropdown = new StatusSelectorBlock($booking, array(
            'id'       => 'post-status',
            'name'     => 'post_status'
        ));

        $statusTooltip  = new Tooltip(sprintf(
            '%s<hr/>%s',
            __('Confirmed bookings are saved bookings that represent a booking that will happen. Its date(s) and time(s) will be blocked from the front-end calendar so other people cannot make bookings at that time. These bookings will also appear in your Calendar.', 'eddbk'),
            __('Draft bookings are bookings that are just saved in the database. The system does not acknowledge them and they are simply for your convenience. They can be set to "Confirmed" at a later date. These bookings will not appear on your Calendar.', 'eddbk')
        ), Tooltip::ALIGN_1_LEFT, Tooltip::ALIGN_2_BOTTOM);

        $saveButton = new InputTag(
            'submit', // type
            'submit', // id
            'submit', // name
            __('Save Booking', 'eddbk'), array(
                'class' => 'button button-primary right'
            )
        );

        $clear = new RegularTag('div', array('class' => 'clear'));

        return $statusDropdown . $statusTooltip . $saveButton . $clear;
    }
}
