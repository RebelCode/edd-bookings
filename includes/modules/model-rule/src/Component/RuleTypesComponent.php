<?php

namespace RebelCode\EddBookings\Component;

use \RebelCode\EddBookings\Block\Html\DumpBlock;
use \RebelCode\EddBookings\Model\CallbackRuleType;
use \RebelCode\EddBookings\Registry\RuleTypeRegistryInterface;
use \RebelCode\EddBookings\System\Component\AbstractBaseComponent;

/**
 * Description of RuleTypesComponent
 *
 * @since [*next-version*]
 */
class RuleTypesComponent extends AbstractBaseComponent
{
    
    public function onAppReady()
    {
        add_action(
            'avail_builder_rule_types_registration',
            function(RuleTypeRegistryInterface $registry) {
                $registry->register('time_range_rule', new CallbackRuleType(
                    array(
                        'id'       => 'time_range_rule',
                        'name'     => __('Time Range Rule', 'eddbk'),
                        'callback' => function($rule) {
                            return new DumpBlock($rule);
                        }
                    )
                ));
            }
        );
    }

}
