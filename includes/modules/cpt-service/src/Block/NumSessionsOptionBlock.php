<?php

namespace RebelCode\EddBookings\CustomPostType\Service\Block;

use \RebelCode\EddBookings\Block\Html\InputTag;
use \RebelCode\EddBookings\Block\Html\RegularTag;

/**
 * Description of NumSessionsOptionBlock
 *
 * @since [*next-version*]
 */
class NumSessionsOptionBlock extends AbstractServiceOptionBlock
{

    protected function _getOutput()
    {
        $service     = $this->getService();
        $minSessions = $service->getMinSessions();
        $maxSessions = $service->getMaxSessions();

        $minField = new InputTag(
            'number',
            'edd-bk-min-sessions',
            'service[min_sessions]',
            $minSessions,
            array(
                'min'         => 1,
                'step'        => 1,
                'placeholder' => __('Minimum', 'eddbk')
            )
        );

        $maxField = new InputTag(
            'number',
            'edd-bk-max-sessions',
            'service[max_sessions]',
            $maxSessions,
            array(
                'min'         => 1,
                'step'        => 1,
                'placeholder' => __('Maximum', 'eddbk')
            )
        );

        return sprintf('%1$s %2$s %3$s %4$s',
            $minField,
            new RegularTag('span', array(), __('to', 'eddbk')),
            $maxField,
            new RegularTag('span', array(), __('sessions', 'eddbk'))
        );
    }

}
