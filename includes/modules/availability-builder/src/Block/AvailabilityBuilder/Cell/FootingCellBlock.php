<?php

namespace RebelCode\EddBookings\Block\AvailabilityBuilder\Cell;

/**
 * Description of FootingCellBlock
 *
 * @since [*next-version*]
 */
class FootingCellBlock extends BaseCellBlock
{
    /**
     * The footing cell class attribute
     *
     * @since [*next-version*]
     */
    const FOOTING_CELL_CLASS = 'edd-bk-footing';

    /**
     * Gets the footing cell HTML class.
     *
     * @since [*next-version*]
     *
     * @return string
     */
    public function getFootingCellClass()
    {
        return static::FOOTING_CELL_CLASS;
    }

    /**
     * Gets the HTML class attribute pattern.
     *
     * @since [*next-version*]
     *
     * @return string
     */
    public function getCellClassPattern()
    {
        return sprintf('%1$s %2$s', $this->getFootingCellClass(), static::CELL_CLASS_PATTERN);
    }
}
