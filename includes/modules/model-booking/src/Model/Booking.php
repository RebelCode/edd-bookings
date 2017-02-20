<?php

namespace RebelCode\EddBookings\Model;

use \RebelCode\Bookings\Framework\Storage\ResourceModelInterface;
use \RebelCode\Bookings\Model\Booking\SimpleBooking;
use \RebelCode\Diary\DateTime\DateTime;
use \RebelCode\Diary\DateTime\Period;

/**
 * Booking model class.
 *
 * @since [*next-version*]
 */
class Booking extends SimpleBooking
{
    /**
     * Constructor.
     *
     * @since [*next-version*]
     *
     * @param int $id The ID of the booking.
     * @param ResourceModelInterface $resourceModel The resource model instance.
     */
    public function __construct($id, ResourceModelInterface $resourceModel)
    {
        parent::__construct(array());

        $this->setId($id)
            ->setResourceModel($resourceModel);
    }

    /**
     * {@inheritdoc}
     *
     * @since [*next-version*]
     */
    public function getPeriod()
    {
        $default = new DateTime(null, new \DateTimeZone('UTC'));

        return new Period(
            $this->getData('start', $default),
            $this->getData('end', $default->addHour())
        );
    }

    public function getStart()
    {
        return $this->getPeriod()->getStart();
    }

    public function getEnd()
    {
        return $this->getPeriod()->getEnd();
    }

    public function getDuration()
    {
        return $this->getPeriod()->getDuration();
    }

    /**
     * {@inheritdoc}
     *
     * @since [*next-version*]
     */
    public function getCustomerTzOffset()
    {
        return $this->getData('client_timezone', 0);
    }
}
