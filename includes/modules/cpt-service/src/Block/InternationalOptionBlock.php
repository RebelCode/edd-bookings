<?php

namespace RebelCode\EddBookings\CustomPostType\Service\Block;

use \RebelCode\EddBookings\Block\Html\CheckboxBlock;

/**
 * Description of InternationalOptionBlock
 *
 * @since [*next-version*]
 */
class InternationalOptionBlock extends AbstractServiceOptionBlock
{

    /**
     * {@inheritdoc}
     *
     * @since [*next-version*]
     */
    protected function _getOutput()
    {
        return new CheckboxBlock(
            'edd-bk-use-customer-tz',
            'service[use_customer_tz]',
            '1',
            $this->getService()->isInternational()
        );
    }

}
