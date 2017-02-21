<?php

namespace RebelCode\EddBookings\Block\AvailabilityBuilder\Cell;

/**
 * A block for a header cell in the availability builder.
 *
 * @since [*next-version*]
 */
class HeaderCellBlock extends BaseCellBlock
{
    /**
     * The heading cell class attribute
     *
     * @since [*next-version*]
     */
    const HEADING_CELL_CLASS = 'edd-bk-heading';

    /**
     * Gets the heading cell HTML class.
     *
     * @since [*next-version*]
     *
     * @return string
     */
    public function getHeadingCellClass()
    {
        return static::HEADING_CELL_CLASS;
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
        return sprintf('%1$s %2$s', $this->getHeadingCellClass(), static::CELL_CLASS_PATTERN);
    }
}
