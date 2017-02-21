<?php

namespace RebelCode\EddBookings\CustomPostType\Booking\Block;

use \RebelCode\EddBookings\Block\AbstractBlock;
use \RebelCode\EddBookings\Block\Html\CompositeTag;
use \RebelCode\EddBookings\Block\Html\DumpBlock;
use \RebelCode\EddBookings\Block\Html\RegularTag;
use \RebelCode\EddBookings\Controller\BookingController;
use \RebelCode\EddBookings\Model\Booking;

/**
 * Description of DevDataBlock
 *
 * @since [*next-version*]
 */
class DevDataBlock extends AbstractBlock
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
        return new DumpBlock($this->_getBooking()->getData());
    }
}
