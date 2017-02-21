<?php

namespace RebelCode\EddBookings\Block\AvailabilityBuilder\Rule;

use \RebelCode\Bookings\Framework\Registry\ReadableRegistryInterface;
use \RebelCode\EddBookings\Block\AbstractBlock;
use \RebelCode\EddBookings\Block\Html\SelectTag;
use \RebelCode\EddBookings\Model\RuleTypeInterface;

/**
 * A block for a rule type selector element.
 *
 * @since [*next-version*]
 */
class RuleTypeSelectorBlock extends AbstractBlock
{
    /**
     * A collection of rule types.
     *
     * @var ReadableRegistryInterface
     */
    protected $ruleTypes;

    /**
     * The selected rule type.
     *
     * @var RuleTypeInterface
     */
    protected $selected;

    /**
     * Additional HTML attributes.
     *
     * @var array
     */
    protected $attributes;

    /**
     * Constructor.
     *
     * @since [*next-version*]
     *
     * @param ReadableRegistryInterface $ruleTypes The rule types to show in the selector.
     * @param RuleTypeInterface $selected The selected rule type. Default: null
     * @param array $attributes Additional HTML attributes. Default: array
     */
    public function __construct(
        ReadableRegistryInterface $ruleTypes,
        RuleTypeInterface $selected = null,
        array $attributes = array()
    ) {
        $this->setRuleTypes($ruleTypes)
            ->setSelected($selected)
            ->setAttributes($attributes);
    }

    /**
     * Retrieves the rule types.
     *
     * @since [*next-version*]
     *
     * @return ReadableRegistryInterface A registry of rule types.
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
     * @param ReadableRegistryInterface $ruleTypes A registry of rule types.
     *
     * @return $this This instance.
     */
    public function setRuleTypes(ReadableRegistryInterface $ruleTypes)
    {
        $this->ruleTypes = $ruleTypes;

        return $this;
    }

    /**
     * Gets the selected rule type.
     *
     * @since [*next-version*]
     *
     * @return RuleTypeInterface The rule type instance.
     */
    public function getSelected()
    {
        return $this->selected;
    }

    /**
     * Sets the selected rule type.
     *
     * @since [*next-version*]
     *
     * @param RuleTypeInterface|null $selected The rule type instance or null. Default: null
     *
     * @return $this This instance.
     */
    public function setSelected(RuleTypeInterface $selected = null)
    {
        $this->selected = $selected;
        return $this;
    }

    /**
     * Gets the HTML attributes.
     *
     * @since [*next-version*]
     *
     * @return array An array of HTML attribute values mapped via their names.
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * Sets the HTML attributes.
     *
     * @since [*next-version*]
     *
     * @param array $attributes An array of HTML attribute values mapped via their names.
     *
     * @return $this This instance.
     */
    public function setAttributes($attributes)
    {
        $this->attributes = $attributes;

        return $this;
    }

    /**
     * Processes the rule types to prepare the items to be shown in the selector.
     *
     * If rule types contain a dash "-", the text before the dash will be treated as a
     * group name. The text after the dash will be used as the rule name.
     *
     * @since [*next-version*]
     *
     * @return array An array of rule type names mapped to their IDs and other possible
     *               sub-arrays in the case of groups.
     */
    protected function _getProcessedItems()
    {
        $items = array();

        foreach($this->getRuleTypes()->items() as $_key => $_ruleType) {
            $_id        = $_ruleType->getId();
            $_fullName  = $_ruleType->getName();
            $_nameParts = array_map('trim', explode('-', $_fullName, 2));
            $_target    = &$items;

            if (count($_nameParts) > 1 && strlen($_nameParts) > 0) {
                $_group = $_nameParts[0];
                $items[$_group] = isset($items[$_group])
                    ? $items[$_group]
                    : array();
                $_target = &$items[$_group];
            }

            $_target[$_id] = $_nameParts[0];
        }

        return $items;
    }

    /**
     * Gets the selected rule type ID.
     *
     * @since [*next-version*]
     *
     * @return string The rule type's ID or an empty string if the selector has no selected rule type.
     */
    protected function _getSelectedRuleTypeId()
    {
        $ruleType = $this->getSelected();

        return ($ruleType instanceof RuleTypeInterface)
            ? $ruleType->getId()
            : '';
    }

    /**
     * {@inheritdoc}
     *
     * @since [*next-version*]
     */
    protected function _getOutput()
    {
        return new SelectTag(
            $this->_getProcessedItems(),
            $this->_getSelectedRuleTypeId(),
            $this->getAttributes()
        );
    }
}
