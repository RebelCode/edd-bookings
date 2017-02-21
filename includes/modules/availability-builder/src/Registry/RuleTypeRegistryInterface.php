<?php

namespace RebelCode\EddBookings\Registry;

use \RebelCode\Bookings\Framework\Registry\ReadableRegistryInterface;
use \RebelCode\EddBookings\Model\RuleTypeInterface;

/**
 * Defines a registry of rule types.
 *
 * @since [*next-version*]
 */
interface RuleTypeRegistryInterface extends ReadableRegistryInterface
{
    /**
     * {@inheritdoc}
     *
     * @since [*next-version*]
     *
     * @param string $id The rule type ID.
     *
     * @return RuleTypeInterface The rule type registered with the given ID.
     */
    public function get($id);
}
