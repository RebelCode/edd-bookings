<?php

namespace RebelCode\EddBookings\Block\AvailabilityBuilder\Row;

use \RebelCode\Bookings\Framework\Registry\ReadableRegistryInterface;
use \RebelCode\Bookings\Model\Rule\RuleInterface;
use \RebelCode\EddBookings\Block\AvailabilityBuilder\Cell\BaseCellBlock;
use \RebelCode\EddBookings\Block\AvailabilityBuilder\Row\AbstractRowBlock;
use \RebelCode\EddBookings\Block\AvailabilityBuilder\Rule\RuleTypeSelectorBlock;
use \RebelCode\EddBookings\Block\Html\FaIcon;
use \RebelCode\EddBookings\Registry\RuleTypeRegistryInterface;

/**
 * A rule row block.
 *
 * A single rule row represents an availability row that contains the dragging handle, the
 * remove button, the rule type selector and the rule block.
 *
 * @since [*next-version*]
 */
class RuleRowBlock extends AbstractRowBlock
{

    /**
     * The rule to render.
     *
     * @since [*next-version*]
     *
     * @var RuleInterface
     */
    protected $rule;

    /**
     * The rule types.
     *
     * @since [*next-version*]
     *
     * @var RuleTypeRegistryInterface
     */
    protected $ruleTypes;

    /**
     * Constructor.
     *
     * @param RuleInterface $rule
     * @param ReadableRegistryInterface $ruleTypes
     */
    public function __construct(
        RuleInterface $rule,
        RuleTypeRegistryInterface $ruleTypes
    ) {
        $this->setRule($rule)
            ->setRuleTypes($ruleTypes);
    }

    public function getRule()
    {
        return $this->rule;
    }

    public function setRule(RuleInterface $rule)
    {
        $this->rule = $rule;
        return $this;
    }

    public function getRuleTypes()
    {
        return $this->ruleTypes;
    }

    public function setRuleTypes(RuleTypeRegistryInterface $ruleTypeRegistry)
    {
        $this->ruleTypes = $ruleTypeRegistry;

        return $this;
    }

    protected function _getRuleTypeNames()
    {
        $names = array();

        foreach ($this->getRuleTypes()->items() as $type) {
            $parts = explode('-', $type->getName());

            if (count($parts) === 1 || trim($parts[0]) === '') {
                $names[$type->getId()] = trim($parts[0]);
                continue;
            }

            $group = trim($parts[0]);
            $name  = trim($parts[1]);

            if (!isset($names[$group])) {
                $names[$group] = array();
            }

            $names[$group][$type->getId()] = $name;
        }

        return $names;
    }

    protected function _getDefaultRuleOutput()
    {
        return implode('', array(
            new BaseCellBlock('start', ''),
            new BaseCellBlock('end', ''),
            new BaseCellBlock('available', '')
        ));
    }

    public function getContent()
    {
        $rule       = $this->getRule();
        $ruleTypeId = $rule->getType();
        $registry   = $this->getRuleTypes();
        $ruleType   = $this->getRuleTypes()->get($ruleTypeId);
        $ruleOutput = is_null($ruleType)
            ? $this->_getDefaultRuleOutput()
            : $this->getRuleTypes()->get($ruleTypeId);

        return array(
            new BaseCellBlock(
                'move', new FaIcon('arrows-v', array('class' => 'edd-bk-move-handle'))
            ),
            new BaseCellBlock(
                'time-unit', new RuleTypeSelectorBlock(
                    $registry,
                    $ruleType
                )
            ),
            $ruleOutput,
            new BaseCellBlock(
                'remove', new FaIcon('times', array('class' => 'edd-bk-remove-handle'))
            )
        );
    }
}
