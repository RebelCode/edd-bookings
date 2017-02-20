<?php

namespace RebelCode\EddBookings\Model;

use \RebelCode\Bookings\Framework\Storage\ResourceModelInterface;
use \RebelCode\Bookings\Model\Availability\RuleExpressionAvailability;
use \RebelCode\Bookings\Model\Service\SimpleSessionService;

/**
 * Service model class.
 *
 * @since [*next-version*]
 */
class Service extends SimpleSessionService
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
     *
     * @return bool
     */
    public function getBookingsEnabled()
    {
        return (bool) $this->getData('bookings_enabled', false);
    }

    /**
     *
     * @return SessionUnit
     */
    public function getSessionUnit()
    {
        return $this->getData('session_unit', SessionUnit::HOURS());
    }

    public function isInternational()
    {
        return (bool) $this->getData('international', false);
    }

    /**
     * {@inheritdoc}
     *
     * @since [*next-version*]
     *
     * @return RuleExpressionAvailability
     */
    public function getAvailability()
    {
        return $this->getData('availability');
    }
}
