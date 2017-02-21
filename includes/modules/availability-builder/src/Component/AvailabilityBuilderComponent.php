<?php

namespace RebelCode\EddBookings\Component;

use \Dhii\App\AppInterface;
use \Dhii\WpEvents\Event;
use \Psr\EventManager\EventManagerInterface;
use \RebelCode\EddBookings\Registry\RuleTypeRegistryInterface;
use \RebelCode\EddBookings\System\Component\AbstractBaseComponent;
use \RebelCode\EddBookings\System\Component\ComponentInterface;

/**
 * A component for an availability builder.
 *
 * @since [*next-version*]
 */
class AvailabilityBuilderComponent extends AbstractBaseComponent implements ComponentInterface
{
    /**
     * The rule type registry.
     *
     * @since [*next-version*]
     *
     * @var RuleTypeRegistryInterface
     */
    protected $ruleTypeRegistry;

    /**
     * The event manager.
     *
     * @since [*next-version*]
     *
     * @var EventManagerInterface
     */
    protected $eventManager;

    /**
     * Constructor.
     *
     * @since [*next-version*]
     *
     * @param AppInterface $app The parent app instance.
     * @param EventManagerInterface $eventManager The event manager.
     * @param RuleTypeRegistryInterface $ruleTypeRegistry The rule type registry.
     */
    public function __construct(
        AppInterface $app,
        EventManagerInterface $eventManager,
        RuleTypeRegistryInterface $ruleTypeRegistry
    ) {
        parent::__construct($app);

        $this->setEventManager($eventManager)
            ->setRuleTypeRegistry($ruleTypeRegistry);
    }

    /**
     * Gets the rule type registry.
     *
     * @since [*next-version*]
     *
     * @return RuleTypeRegistryInterface
     */
    public function getRuleTypeRegistry()
    {
        return $this->ruleTypeRegistry;
    }

    /**
     * Sets the rule type registry.
     *
     * @since [*next-version*]
     *
     * @param RuleTypeRegistryInterface $ruleTypeRegistry The new rule type registry instance.
     *
     * @return $this This instance.
     */
    public function setRuleTypeRegistry(RuleTypeRegistryInterface $ruleTypeRegistry)
    {
        $this->ruleTypeRegistry = $ruleTypeRegistry;

        return $this;
    }

    /**
     * Gets the event manager.
     *
     * @since [*next-version*]
     *
     * @return EventManagerInterface
     */
    public function getEventManager()
    {
        return $this->eventManager;
    }

    /**
     * Sets the event manager.
     *
     * @since [*next-version*]
     *
     * @param EventManagerInterface $eventManager The event manager.
     *
     * @return $this This instance.
     */
    public function setEventManager($eventManager)
    {
        $this->eventManager = $eventManager;

        return $this;
    }

    public function acceptRegistrations()
    {
        $this->getEventManager()
            ->trigger('avail_builder_rule_type_registration', $this, array(
            'registry'     => $this->getRuleTypeRegistry()
        ));
    }

    /**
     * {@inheritdoc}
     *
     * @since [*next-version*]
     */
    public function onAppReady()
    {
        $this->getEventManager()->attach('init', $this->_callback('acceptRegistrations'));
    }
}
