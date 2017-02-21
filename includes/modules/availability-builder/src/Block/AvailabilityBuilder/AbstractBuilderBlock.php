<?php

namespace RebelCode\EddBookings\Block\AvailabilityBuilder;

use \RebelCode\EddBookings\CustomPostType\Service\Block\AbstractServiceOptionBlock;
use \RebelCode\EddBookings\Registry\RuleTypeRegistryInterface;

/**
 * Description of AbstractBuilderBlock
 *
 * @since [*next-version*]
 */
abstract class AbstractBuilderBlock extends AbstractServiceOptionBlock
{
    /**
     * The rule types.
     *
     * @since [*next-version*]
     *
     * @var RuleTypeRegistryInterface
     */
    protected $ruleTypes;

    /**
     * Gets the rule types.
     *
     * @since [*next-version*]
     *
     * @return RuleTypeRegistryInterface The registry of rule types.
     */
    public function getRuleTypes()
    {
        return $this->ruleTypes;
    }

    /**
     * Sets the rule types.
     *
     * @since [*next-version*]
     *
     * @param RuleTypeRegistryInterface $ruleTypeRegistry The rule types registry.
     *
     * @return $this This instance.
     */
    public function setRuleTypes(RuleTypeRegistryInterface $ruleTypeRegistry)
    {
        $this->ruleTypes = $ruleTypeRegistry;

        return $this;
    }
}
