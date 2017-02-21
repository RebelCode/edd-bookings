<?php

namespace RebelCode\EddBookings\Block\AvailabilityBuilder\Rule;

/**
 *
 * @since [*next-version*]
 */
interface RuleBlockInterface
{
    /**
     * Gets the rule being rendered.
     *
     * @since [*next-version*]
     *
     * @return RuleInterface The rule.
     */
    public function getRule();
}
