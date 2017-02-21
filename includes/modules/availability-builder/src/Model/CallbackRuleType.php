<?php

namespace RebelCode\EddBookings\Model;

use \RebelCode\Bookings\Model\Rule\RuleInterface;

/**
 * Description of CallbackRuleType
 *
 * @since [*next-version*]
 */
class CallbackRuleType extends AbstractBaseRuleType
{
    /**
     * {@inheritdoc}
     *
     * @since [*next-version*]
     */
    public function getBlock(RuleInterface $rule)
    {
        return call_user_func_array($this->getData('callback'), array($rule));
    }
}
