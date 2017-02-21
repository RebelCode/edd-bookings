<?php

namespace RebelCode\EddBookings\Registry;

use \RebelCode\Bookings\Framework\Registry\AbstractRegistry;
use \RebelCode\EddBookings\Model\RuleTypeInterface;

/**
 * A registry of rule types.
 *
 * @since [*next-version*]
 */
class RuleTypeRegistry extends AbstractRegistry implements RuleTypeRegistryInterface
{
    /**
     * Constructor
     *
     * @since [*next-version*]
     */
    public function __construct()
    {
        $this->clear();
    }

    /**
     * {@inheritdoc}
     *
     * @since [*next-version*]
     */
    public function validate($item)
    {
        return ($item instanceof RuleTypeInterface);
    }
}
