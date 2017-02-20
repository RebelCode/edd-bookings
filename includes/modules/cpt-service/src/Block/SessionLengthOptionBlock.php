<?php

namespace RebelCode\EddBookings\CustomPostType\Service\Block;

use \RebelCode\EddBookings\Block\Html\InputTag;
use \RebelCode\EddBookings\Block\Html\SelectTag;

/**
 * Description of SessionLengthOptionBlock
 *
 * @since [*next-version*]
 */
class SessionLengthOptionBlock extends AbstractServiceOptionBlock
{
    /**
     * {@inheritdoc}
     *
     * @since [*next-version*]
     */
    protected function _getOutput()
    {
        $service       = $this->getService();
        $unit          = $service->getSessionUnit();
        $lengthSeconds = $service->getSessionLength();
        $lengthUnits   = $lengthSeconds / $unit->getValue();
        $selectedUnit  = strtolower($unit->getKey());

        $lengthField   = new InputTag(
            'number',
            'edd-bk-session-length',
            'service[session_length]',
            $lengthUnits,
            array(
                'min'  => 0,
                'step' => 1
            )
        );
        $unitSelector = new SelectTag(
            $this->_getUnits(),
            $selectedUnit,
            array(
                'id'   => 'edd-bk-session-unit',
                'name' => 'service[session_unit]'
            )
        );

        return $lengthField . $unitSelector;
    }

    protected function _getUnits()
    {
        return array(
            'seconds' => __('seconds', 'eddbk'),
            'minutes' => __('minutes', 'eddbk'),
            'hours'   => __('hours', 'eddbk'),
            'days'    => __('seconds', 'eddbk'),
            'weeks'   => __('weeks', 'eddbk')
        );
    }
}
