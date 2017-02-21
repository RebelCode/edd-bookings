<?php

namespace RebelCode\EddBookings\Model;

use \RebelCode\Bookings\Model\Rule\RuleInterface;

/**
 * Something that defines a rule type shown in the availability builder.
 *
 * @since [*next-version*]
 */
interface RuleTypeInterface
{
    /**
     * Gets the unique ID of the rule type.
     *
     * @since [*next-version*]
     *
     * @return string The ID of this rule type.
     */
    public function getId();

    /**
     * Gets the user-friendly name of the rule type.
     *
     * @since [*next-version*]
     *
     * @return string The name of this rule type.
     */
    public function getName();

    /**
     * Retrieves the block that renders the options for a given rule instance.
     *
     * @since [*next-version*]
     *
     * @param RuleInterface $rule The rule whose options are to be rendered.
     *
     * @return BlockInterface The block for the given rule instance.
     */
    public function getBlock(RuleInterface $rule);
}
