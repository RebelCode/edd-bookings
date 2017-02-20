<?php

namespace RebelCode\EddBookings\CustomPostType\Service\Block;

use \RebelCode\EddBookings\Block\Html\InputTag;

/**
 * Description of SessionCostOptionBlock
 *
 * @since [*next-version*]
 */
class SessionPriceOptionBlock extends AbstractServiceOptionBlock
{
    /**
     * {@inheritdoc}
     *
     * @since [*next-version*]
     */
    protected function _getOutput()
    {
        $service      = $this->getService();
        $sessionPrice = $service->getSessionPrice();

        return new InputTag(
            'number',
            'edd-bk-session-cost',
            'service[session_price]',
            $sessionPrice,
            array(
                'min'  => 0,
                'step' => 0.01
            )
        );
    }

}
