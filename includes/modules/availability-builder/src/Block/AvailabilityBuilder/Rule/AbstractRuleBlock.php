<?php

namespace RebelCode\EddBookings\Block\AvailabilityBuilder\Rule;

use \RebelCode\Bookings\Model\Availability\Rule\RuleInterface;
use \RebelCode\EddBookings\Block\AbstractBlock;

/**
 * Basic functionality for a rule block.
 *
 * A rule block represents a portion of an availability row. Specifically, the portion that
 * consists of the rule options.
 *
 * @since [*next-version*]
 */
abstract class AbstractRuleBlock extends AbstractBlock implements RuleBlockInterface
{
    /**
     * The rule being rendered.
     *
     * @since [*next-version*]
     *
     * @var RuleInterface
     */
    protected $rule;

    /**
     * {@inheritdoc}
     *
     * @since [*next-version*]
     */
    public function getRule()
    {
        return $this->rule;
    }

    /**
     * Sets the rule to render.
     *
     * @since [*next-version*]
     *
     * @param RuleInterface $rule The rule instance.
     *
     * @return $this This instance.
     */
    public function setRule(RuleInterface $rule = null)
    {
        $this->rule = $rule;

        return $this;
    }
}
