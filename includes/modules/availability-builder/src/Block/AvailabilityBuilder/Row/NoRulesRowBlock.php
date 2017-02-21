<?php

namespace RebelCode\EddBookings\Block\AvailabilityBuilder\Row;

use \RebelCode\EddBookings\Block\AvailabilityBuilder\Cell\BaseCellBlock;
use \RebelCode\EddBookings\Block\Html\CompositeTag;
use \RebelCode\EddBookings\Block\Html\FaIcon;

/**
 * Description of NoRulesRowBlock
 *
 * @since [*next-version*]
 */
class NoRulesRowBlock extends AbstractRowBlock
{
    const IF_NO_RULES_CLASS = 'edd-bk-if-no-rules';

    public function getRowClass()
    {
        return sprintf('%1$s %2$s', parent::getRowClass(), static::IF_NO_RULES_CLASS);
    }
    
    public function getContent()
    {
        return new BaseCellBlock('no-rules', array(
            new CompositeTag('span', array(), array(
                new FaIcon('info-circle'),
                __('You have no availability times set up! Click the "Add" button below to get started.', 'eddbk')
            ))
        ));
    }
}
