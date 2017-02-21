<?php

namespace RebelCode\EddBookings\Block\AvailabilityBuilder;

use \RebelCode\EddBookings\Block\AvailabilityBuilder\Row\NoRulesRowBlock;
use \RebelCode\EddBookings\Block\AvailabilityBuilder\Row\RuleRowBlock;
use \RebelCode\EddBookings\Block\Html\CompositeTag;
use \RebelCode\EddBookings\Model\Service;
use \RebelCode\EddBookings\Registry\RuleTypeRegistryInterface;

/**
 * Description of AvailabilityBuilderBody
 *
 * @since [*next-version*]
 */
class BodyBlock extends AbstractBuilderBlock
{
    const BODY_CLASS = 'edd-bk-body';

    /**
     * Constructor.
     *
     * @param Service $service
     * @param RuleTypeRegistryInterface $ruleTypes
     */
    public function __construct(
        Service $service,
        RuleTypeRegistryInterface $ruleTypes
    ) {
        parent::__construct($service);

        $this
            ->setRuleTypes($ruleTypes);
    }

    public function getBodyClass()
    {
        return static::BODY_CLASS;
    }

    public function getRows()
    {
        $rules = $this->getService()->getAvailability()->getRules();
        $rows = array();

        foreach ($rules as $_rule) {
            $rows[] = new RuleRowBlock(
                $_rule,
                $this->getRuleTypes()
            );
        }

        return $rows;
    }

    public function builtInRows()
    {
        return array(
            new NoRulesRowBlock()
        );
    }

    protected function _getOutput()
    {
        $rows = array_merge($this->builtInRows(), $this->getRows());

        return new CompositeTag('div', array('class' => $this->getBodyClass()), $rows);
    }

}
