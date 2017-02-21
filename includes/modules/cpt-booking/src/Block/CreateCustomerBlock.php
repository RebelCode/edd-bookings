<?php

namespace RebelCode\EddBookings\CustomPostType\Booking\Block;

use \RebelCode\EddBookings\Block\AbstractBlock;
use \RebelCode\EddBookings\Block\Html\CompositeTag;
use \RebelCode\EddBookings\Block\Html\FaIcon;
use \RebelCode\EddBookings\Block\Html\FaSpinningIcon;
use \RebelCode\EddBookings\Block\Html\InputTag;
use \RebelCode\EddBookings\Block\Html\RegularTag;
use \RebelCode\WordPress\Admin\Tooltip;

/**
 * Description of CreateCustomerBlock
 *
 * @since [*next-version*]
 */
class CreateCustomerBlock extends AbstractBlock
{

    protected function _getOutput()
    {
        $nameSection = new CompositeTag('div', array(), array(
            new CompositeTag('label', array('for' => 'customer-name'), array(
                new RegularTag('span', array(), __('Full Name', 'eddbk')),
                new Tooltip(__('The first and last name of the new customer.', 'eddbk'))
            )),
            new InputTag('text', 'customer-name', 'customer_name', '')
        ));
        
        $emailSection = new CompositeTag('div', array(), array(
            new CompositeTag('label', array('for' => 'customer-email'), array(
                new RegularTag('span', array(), __('Email Address', 'eddbk')),
                new Tooltip(__('The email address for the new customer.', 'eddbk'))
            )),
            new InputTag('email', 'customer-email', 'customer_email', '')
        ));

        $button = new CompositeTag('div', array(), array(
            new RegularTag('label'),
            new CompositeTag('button',
                array(
                    'id'    => 'create-customer-btn',
                    'class' => 'button button-secondary',
                    'type'  => 'button'
                ),
                array(
                    new CompositeTag('span', array(), array(
                        new FaIcon('check'),
                        __('Create New Customner', 'eddbk')
                    )),
                    new FaSpinningIcon('spinner', array('class' => 'edd-bk-loading'))
                )
            ),
            new RegularTag('span', array('id' => 'create-customer-error'))
        ));

        return $nameSection . $emailSection . $button;
    }

}
