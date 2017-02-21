<?php

namespace RebelCode\EddBookings\Block\AvailabilityBuilder\Row;

use \RebelCode\EddBookings\Block\AvailabilityBuilder\Cell\HeaderCellBlock;

/**
 * A block for the header row in the availability builder.
 *
 * @since [*next-version*]
 */
class HeaderRowBlock extends AbstractRowBlock
{
    /**
     * The HTML class for the header row.
     *
     * @since [*next-version*]
     */
    const HEADER_ROW_CLASS = 'edd-bk-header';

    /**
     * Gets the HTML class for this header row element.
     *
     * @since [*next-version*]
     *
     * @return string
     */
    public function getHeaderRowClass()
    {
        return static::HEADER_ROW_CLASS;
    }

    /**
     * {@inheritdoc}
     *
     * @since [*next-version*]
     */
    public function getRowClass()
    {
        return sprintf('%1$s %2$s', parent::getRowClass(), $this->getHeaderRowClass());
    }

    public function getHeadings()
    {
        return array(
            'move'      => '',
            'time-unit' => __('Time Unit', 'eddbk'),
            'start'     => __('Start', 'eddbk'),
            'end'       => __('End', 'eddbk'),
            'available' => __('Available', 'eddbk'),
            'remove'    => '',
        );
    }

    public function getContent()
    {
        $blocks = array();

        foreach ($this->getHeadings() as $_col => $_label) {
            $blocks[] = new HeaderCellBlock($_col, $_label);
        }

        return $blocks;
    }
}
