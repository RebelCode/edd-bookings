<?php

namespace RebelCode\EddBookings\Model;

use \RebelCode\Bookings\Framework\Model\BaseModel;

/**
 * Abstract implementation of the base functionality for a rule type,
 *
 * @since [*next-version*]
 */
abstract class AbstractBaseRuleType extends BaseModel implements RuleTypeInterface
{
    /**
     * {@inheritdoc}
     *
     * @since [*next-version*]
     */
    public function getName()
    {
        return $this->getData('name', __('No name', 'eddbk'));
    }
}
