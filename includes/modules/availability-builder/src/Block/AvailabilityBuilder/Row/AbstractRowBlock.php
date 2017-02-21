<?php

namespace RebelCode\EddBookings\Block\AvailabilityBuilder\Row;

use \RebelCode\EddBookings\Block\AbstractBlock;
use \RebelCode\EddBookings\Block\BlockInterface;
use \RebelCode\EddBookings\Block\Html\CompositeTag;

/**
 * A block for a single row in the availability builder.
 *
 * @since [*next-version*]
 */
abstract class AbstractRowBlock extends AbstractBlock
{
    /**
     * The HTML class for a row element.
     *
     * @since [*next-version*]
     */
    const ROW_CLASS = 'edd-bk-row';

    /**
     * Gets the contents of the row.
     *
     * @since [*next-version*]
     *
     * @return string|BlockInterface
     */
    abstract public function getContent();

    /**
     * Gets the HTML class for this row element.
     *
     * @since [*next-version*]
     *
     * @return string
     */
    public function getRowClass()
    {
        return static::ROW_CLASS;
    }

    /**
     * {@inheritdoc}
     *
     * @since [*next-version*]
     */
    protected function _getOutput()
    {
        $content    = $this->getContent();
        $contentArr = is_array($content)
            ? $content
            : array($content);

        return new CompositeTag('div', array('class' => $this->getRowClass()), $contentArr);
    }
}
